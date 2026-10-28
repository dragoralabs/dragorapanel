import crypto from "node:crypto";
import type { NextFunction, Request, Response } from "express";

export type UserRole = "super_admin" | "admin" | "staff";

export interface AuthUser {
  uid: string;
  email: string;
  displayName: string;
  role: UserRole;
}

export interface SessionRecord {
  user: AuthUser;
  expiresAt: number;
  pinUnlockedUntil: number;
}

interface AuthContext {
  token: string;
  session: SessionRecord;
}

const SESSION_COOKIE = "playit_session";
const SESSION_TTL_MS = 12 * 60 * 60 * 1000;
export const PIN_UNLOCK_MS = 15 * 60 * 1000;
const SCRYPT_KEYLEN = 64;
const sessions = new Map<string, SessionRecord>();
const rateBuckets = new Map<string, { count: number; resetAt: number }>();

export function normalizeRole(value: unknown): UserRole {
  return value === "super_admin" || value === "admin" || value === "staff"
    ? value
    : "staff";
}

export function isAdminRole(role: string | undefined | null): boolean {
  return role === "super_admin" || role === "admin";
}

export function hashSecret(secret: string): string {
  const salt = crypto.randomBytes(16);
  const derived = crypto.scryptSync(secret, salt, SCRYPT_KEYLEN);
  return `scrypt$${salt.toString("base64url")}$${derived.toString("base64url")}`;
}

export function verifySecret(secret: string, stored?: string | null): boolean {
  if (!stored) return false;
  const [scheme, saltValue, hashValue] = stored.split("$");
  if (scheme !== "scrypt" || !saltValue || !hashValue) return false;
  try {
    const expected = Buffer.from(hashValue, "base64url");
    const derived = crypto.scryptSync(
      secret,
      Buffer.from(saltValue, "base64url"),
      expected.length
    );
    return expected.length === derived.length && crypto.timingSafeEqual(expected, derived);
  } catch {
    return false;
  }
}

export function createSession(user: AuthUser): string {
  const token = crypto.randomBytes(32).toString("base64url");
  sessions.set(token, {
    user,
    expiresAt: Date.now() + SESSION_TTL_MS,
    pinUnlockedUntil: 0,
  });
  return token;
}

export function destroySession(token?: string | null): void {
  if (token) sessions.delete(token);
}

export function revokeSessionsForEmail(email: string): void {
  const normalized = email.trim().toLowerCase();
  for (const [token, session] of sessions) {
    if (session.user.email.toLowerCase() === normalized) sessions.delete(token);
  }
}

export function readSession(req: Request): AuthContext | null {
  const token = readCookie(req, SESSION_COOKIE);
  if (!token) return null;
  const session = sessions.get(token);
  if (!session || session.expiresAt <= Date.now()) {
    sessions.delete(token);
    return null;
  }
  return { token, session };
}

export function isPinUnlocked(session: SessionRecord): boolean {
  return session.pinUnlockedUntil > Date.now();
}

export function unlockPin(session: SessionRecord): number {
  session.pinUnlockedUntil = Date.now() + PIN_UNLOCK_MS;
  return session.pinUnlockedUntil;
}

export function lockPin(session: SessionRecord): void {
  session.pinUnlockedUntil = 0;
}

export function lockAllPins(): void {
  for (const session of sessions.values()) session.pinUnlockedUntil = 0;
}

export function requireAuth(req: Request, res: Response, next: NextFunction): void {
  const auth = readSession(req);
  if (!auth) {
    res.status(401).json({ error: "AUTHENTICATION_REQUIRED" });
    return;
  }
  res.locals.auth = auth;
  next();
}

export function requireRole(...roles: UserRole[]) {
  return (_req: Request, res: Response, next: NextFunction): void => {
    const auth = res.locals.auth as AuthContext | undefined;
    if (!auth) {
      res.status(401).json({ error: "AUTHENTICATION_REQUIRED" });
      return;
    }
    if (!roles.includes(auth.session.user.role)) {
      res.status(403).json({ error: "FORBIDDEN" });
      return;
    }
    next();
  };
}

export function getAuthContext(res: Response): AuthContext | undefined {
  return res.locals.auth as AuthContext | undefined;
}

export function setSessionCookie(req: Request, res: Response, token: string): void {
  res.cookie(SESSION_COOKIE, token, {
    ...cookieOptions(req),
    maxAge: SESSION_TTL_MS,
  });
}

export function clearSessionCookie(req: Request, res: Response): void {
  res.clearCookie(SESSION_COOKIE, cookieOptions(req));
}

export function consumeRateLimit(
  key: string,
  limit: number,
  windowMs: number
): { allowed: boolean; retryAfter: number; remaining: number } {
  const now = Date.now();
  const bucket = rateBuckets.get(key);
  if (!bucket || bucket.resetAt <= now) {
    rateBuckets.set(key, { count: 1, resetAt: now + windowMs });
    return { allowed: true, retryAfter: 0, remaining: Math.max(0, limit - 1) };
  }
  if (bucket.count >= limit) {
    return {
      allowed: false,
      retryAfter: Math.max(1, Math.ceil((bucket.resetAt - now) / 1000)),
      remaining: 0,
    };
  }
  bucket.count += 1;
  return {
    allowed: true,
    retryAfter: 0,
    remaining: Math.max(0, limit - bucket.count),
  };
}

export function resetRateLimit(key: string): void {
  rateBuckets.delete(key);
}

export function clientAddress(req: Request): string {
  return req.ip || req.socket.remoteAddress || "unknown";
}

function cookieOptions(req: Request): {
  httpOnly: boolean;
  sameSite: "strict";
  secure: boolean;
  path: string;
} {
  const forwarded = String(req.get("x-forwarded-proto") || "")
    .split(",")[0]
    .trim()
    .toLowerCase();
  return {
    httpOnly: true,
    sameSite: "strict",
    secure:
      process.env.PANEL_COOKIE_SECURE === "1" ||
      req.secure ||
      forwarded === "https",
    path: "/",
  };
}

function readCookie(req: Request, name: string): string | null {
  const header = req.headers.cookie;
  if (!header) return null;
  for (const part of header.split(";")) {
    const separator = part.indexOf("=");
    if (separator < 0) continue;
    if (part.slice(0, separator).trim() === name) {
      return decodeURIComponent(part.slice(separator + 1).trim());
    }
  }
  return null;
}

const pruneTimer = setInterval(() => {
  const now = Date.now();
  for (const [token, session] of sessions) {
    if (session.expiresAt <= now) sessions.delete(token);
  }
  for (const [key, bucket] of rateBuckets) {
    if (bucket.resetAt <= now) rateBuckets.delete(key);
  }
}, 10 * 60 * 1000);
pruneTimer.unref();
