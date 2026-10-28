import crypto from "node:crypto";
import fs from "node:fs";
import path from "node:path";
import type { NextFunction, Request, Response } from "express";
import { clientAddress, consumeRateLimit, readSession } from "./server-auth";

export interface RuntimeOptions {
  dataDir: string;
  logRetentionDays?: number;
  auditRetentionDays?: number;
  maxLogBytes?: number;
  maxAuditBytes?: number;
}

type LogLevel = "debug" | "info" | "warn" | "error";

interface AuditRecord {
  v: 1;
  id: string;
  ts: string;
  epochMs: number;
  requestId: string;
  actor: string;
  role: string;
  action: string;
  target: string;
  method: string;
  status: number;
  result: "success" | "denied" | "failure";
  ip: string;
  durationMs: number;
}

export interface AuditQuery {
  limit?: number;
  actor?: string;
  action?: string;
  result?: string;
  requestId?: string;
  since?: number;
  until?: number;
}

const RUNTIME_VERSION = 1;
const counters = {
  startedAt: Date.now(),
  requests: 0,
  responses2xx: 0,
  responses3xx: 0,
  responses4xx: 0,
  responses5xx: 0,
  activeRequests: 0,
  throttled: 0,
  payloadRejected: 0,
  authFailures: 0,
  proxyErrors: 0,
  auditFailures: 0,
};

let options: RuntimeOptions | null = null;
let logDir = "";
let auditDir = "";

const STATIC_PREFIXES = ["/assets/", "/@vite/", "/@fs/", "/src/", "/node_modules/"];

function ensureDir(dir: string): void {
  fs.mkdirSync(dir, { recursive: true, mode: 0o750 });
}

function dayStamp(now = new Date()): string {
  return now.toISOString().slice(0, 10);
}

export function configureRuntime(next: RuntimeOptions): void {
  options = {
    logRetentionDays: 30,
    auditRetentionDays: 90,
    maxLogBytes: 10 * 1024 * 1024,
    maxAuditBytes: 32 * 1024 * 1024,
    ...next,
  };
  logDir = path.join(options.dataDir, "logs");
  auditDir = path.join(options.dataDir, "audit");
  ensureDir(logDir);
  ensureDir(auditDir);
  pruneRuntimeFiles();
}

export function isRuntimeConfigured(): boolean {
  return options !== null;
}

export function logEvent(
  level: LogLevel,
  event: string,
  fields: Record<string, unknown> = {}
): void {
  const entry = {
    v: RUNTIME_VERSION,
    ts: new Date().toISOString(),
    level,
    event,
    ...fields,
  };
  const line = `${JSON.stringify(entry)}\n`;
  if (options) {
    try {
      const file = path.join(logDir, `panel-${dayStamp()}.jsonl`);
      rotateIfNeeded(file, options.maxLogBytes || 10 * 1024 * 1024);
      fs.appendFileSync(file, line, { mode: 0o640 });
    } catch (error) {
      console.error(
        JSON.stringify({
          ts: new Date().toISOString(),
          level: "error",
          event: "runtime.log_write_failed",
          message: error instanceof Error ? error.message : String(error),
        })
      );
    }
  }
  if (level === "error" || level === "warn" || process.env.PANEL_LOG_CONSOLE === "1") {
    const sink = level === "error" ? console.error : level === "warn" ? console.warn : console.log;
    sink(line.trimEnd());
  }
}

function rotateIfNeeded(file: string, maxBytes: number): void {
  try {
    const stat = fs.statSync(file);
    if (stat.size < maxBytes) return;
    const rotated = file.replace(/\.jsonl$/, ".1.jsonl");
    fs.rmSync(rotated, { force: true });
    fs.renameSync(file, rotated);
  } catch (error) {
    if ((error as NodeJS.ErrnoException).code !== "ENOENT") throw error;
  }
}

function pruneRuntimeFiles(): void {
  if (!options) return;
  pruneDirectory(logDir, options.logRetentionDays || 30);
  pruneDirectory(auditDir, options.auditRetentionDays || 90);
}

function pruneDirectory(dir: string, retentionDays: number): void {
  const cutoff = Date.now() - retentionDays * 24 * 60 * 60 * 1000;
  let entries: fs.Dirent[];
  try {
    entries = fs.readdirSync(dir, { withFileTypes: true });
  } catch {
    return;
  }
  for (const entry of entries) {
    if (!entry.isFile() || !entry.name.endsWith(".jsonl")) continue;
    const target = path.join(dir, entry.name);
    try {
      if (fs.statSync(target).mtimeMs < cutoff) fs.rmSync(target, { force: true });
    } catch {
      // A file rotated between readdir and stat is simply skipped.
    }
  }
}

export function requestIdFor(res: Response): string {
  return typeof res.locals.requestId === "string" ? res.locals.requestId : "unassigned";
}

export function sanitizeRequestTarget(rawUrl: string): string {
  let parsed: URL;
  try {
    parsed = new URL(rawUrl, "http://panel.local");
  } catch {
    return "/invalid-url";
  }
  const sensitive = new Set([
    "token",
    "pin",
    "password",
    "secret",
    "code",
    "key",
    "ticket",
    "authorization",
    "sig",
    "signature",
  ]);
  for (const key of Array.from(parsed.searchParams.keys())) {
    if (sensitive.has(key.toLowerCase())) parsed.searchParams.set(key, "[redacted]");
  }
  return `${parsed.pathname}${parsed.search}`;
}

function auditAction(method: string, target: string): string {
  const pathname = target.split("?")[0];
  const rules: Array<[RegExp, string]> = [
    [/^\/api\/settings$/, "settings.update"],
    [/^\/api\/staff$/, "staff.upsert"],
    [/^\/api\/staff\/[^/]+$/, "staff.delete"],
    [/^\/api\/security\/verify-pin$/, "security.pin_verify"],
    [/^\/api\/security\/lock$/, "security.pin_lock"],
    [/^\/api\/security\/update-pin$/, "security.pin_update"],
    [/^\/api\/analytics\/reset$/, "analytics.reset"],
    [/^\/api\/auth\/login$/, "auth.login"],
    [/^\/api\/auth\/logout$/, "auth.logout"],
    [/^\/api\/nodes\/tokens\/rotate$/, "tokens.rotate"],
    [/^\/api\/nodes\/tokens\/[^/]+\/[^/]+$/, "tokens.revoke"],
    [/\/files\/write$/, "files.write"],
    [/\/files\/delete$/, "files.delete"],
    [/\/files\/delete-batch$/, "files.delete_batch"],
    [/\/files\/archive$/, "files.archive"],
    [/\/files\/extract$/, "files.extract"],
    [/\/files\/rename$/, "files.rename"],
    [/\/files\/mkdir$/, "files.mkdir"],
    [/\/files\/download-ticket$/, "files.download_ticket"],
    [/\/servers\/[^/]+\/reinstall$/, "servers.reinstall"],
    [/\/servers\/[^/]+\/install$/, "servers.install"],
    [/\/servers\/[^/]+\/backups\/[^/]+\/restore$/, "backups.restore"],
    [/\/servers\/[^/]+\/backups$/, "backups.create"],
    [/\/servers\/[^/]+\/backups\/[^/]+$/, "backups.delete"],
    [/\/servers\/[^/]+\/databases$/, "databases.create"],
    [/\/servers\/[^/]+\/databases\/[^/]+$/, "databases.delete"],
    [/\/servers$/, "servers.create"],
    [/\/servers\/[^/]+\/start$/, "servers.start"],
    [/\/servers\/[^/]+\/stop$/, "servers.stop"],
    [/\/servers\/[^/]+\/restart$/, "servers.restart"],
    [/\/servers\/[^/]+\/kill$/, "servers.kill"],
    [/\/eggs$/, "eggs.create"],
    [/\/eggs\/[^/]+$/, "eggs.save"],
    [/\/database-hosts$/, "database_hosts.create"],
    [/\/database-hosts\/[^/]+$/, "database_hosts.update"],
  ];
  for (const [pattern, action] of rules) {
    if (pattern.test(pathname)) return action;
  }
  return `http.${method.toLowerCase()}`;
}

function isAuditedRequest(req: Request): boolean {
  if (req.method === "GET" || req.method === "HEAD" || req.method === "OPTIONS") return false;
  const url = req.originalUrl || req.url;
  if (!url.startsWith("/api") && !url.startsWith("/wings") && !url.startsWith("/playit-api")) {
    return false;
  }
  const pathname = url.split("?")[0];
  const excluded = new Set([
    "/api/analytics/visit",
    "/api/security/verify-captcha",
    "/api/auth/send-code",
    "/api/auth/verify",
    "/api/auth/google",
  ]);
  return !excluded.has(pathname);
}

function shouldLogRequest(req: Request): boolean {
  const url = req.originalUrl || req.url;
  if (!url.startsWith("/api") && !url.startsWith("/wings") && !url.startsWith("/playit-api")) {
    if (url !== "/readyz" && url !== "/livez") return false;
  }
  if (url.split("?")[0] === "/api/analytics/visit") return false;
  return !STATIC_PREFIXES.some((prefix) => url.startsWith(prefix));
}

function responseActor(res: Response): { actor: string; role: string } {
  const auth = res.locals.auth as
    | { session?: { user?: { email?: string; role?: string } } }
    | undefined;
  const user = auth?.session?.user;
  return {
    actor: user?.email || "anonymous",
    role: user?.role || "anonymous",
  };
}

function appendAudit(record: AuditRecord): void {
  if (!options) return;
  try {
    const file = path.join(auditDir, `audit-${dayStamp()}.jsonl`);
    rotateIfNeeded(file, options.maxAuditBytes || 32 * 1024 * 1024);
    fs.appendFileSync(file, `${JSON.stringify(record)}\n`, { mode: 0o640 });
  } catch (error) {
    counters.auditFailures += 1;
    logEvent("error", "audit.write_failed", {
      message: error instanceof Error ? error.message : String(error),
    });
  }
}

export function runtimeMiddleware(req: Request, res: Response, next: NextFunction): void {
  const supplied = req.get("x-request-id");
  const requestId =
    supplied && /^[A-Za-z0-9._-]{8,64}$/.test(supplied) ? supplied : crypto.randomUUID();
  const startedAt = process.hrtime.bigint();
  res.locals.requestId = requestId;
  res.setHeader("x-request-id", requestId);
  counters.requests += 1;
  counters.activeRequests += 1;

  let finalized = false;
  const finalize = () => {
    if (finalized) return;
    finalized = true;
    counters.activeRequests = Math.max(0, counters.activeRequests - 1);
    const status = res.statusCode || 0;
    if (status >= 500) counters.responses5xx += 1;
    else if (status >= 400) counters.responses4xx += 1;
    else if (status >= 300) counters.responses3xx += 1;
    else if (status >= 200) counters.responses2xx += 1;
    const durationMs = Number(process.hrtime.bigint() - startedAt) / 1_000_000;
    const target = sanitizeRequestTarget(req.originalUrl || req.url);

    if (shouldLogRequest(req)) {
      const { actor, role } = responseActor(res);
      logEvent(status >= 500 ? "error" : status >= 400 ? "warn" : "info", "http.request", {
        requestId,
        method: req.method,
        target,
        status,
        durationMs: Math.round(durationMs * 10) / 10,
        actor,
        role,
        ip: clientAddress(req),
      });
    }

    if (isAuditedRequest(req)) {
      const { actor, role } = responseActor(res);
      const result: AuditRecord["result"] =
        status < 400 ? "success" : status === 401 || status === 403 ? "denied" : "failure";
      appendAudit({
        v: 1,
        id: `aud_${crypto.randomUUID()}`,
        ts: new Date().toISOString(),
        epochMs: Date.now(),
        requestId,
        actor,
        role,
        action: auditAction(req.method, target),
        target,
        method: req.method,
        status,
        result,
        ip: clientAddress(req),
        durationMs: Math.round(durationMs),
      });
    }
  };

  res.on("finish", finalize);
  res.on("close", finalize);
  next();
}

export function auditRateLimit(
  classname: string,
  limit: number,
  windowMs: number
): (req: Request, res: Response, next: NextFunction) => void {
  return (req, res, next) => {
    const session = readSession(req);
    if (session) res.locals.auth = session;
    const auth = res.locals.auth as
      | { session?: { user?: { email?: string; role?: string } } }
      | undefined;
    const identity = auth?.session?.user?.email || clientAddress(req);
    const key = `route:${classname}:${identity}`;
    const result = consumeRateLimit(key, limit, windowMs);
    res.setHeader("RateLimit-Limit", String(limit));
    res.setHeader("RateLimit-Remaining", String(result.remaining));
    if (!result.allowed) {
      counters.throttled += 1;
      res.setHeader("Retry-After", String(result.retryAfter));
      logEvent("warn", "http.throttled", {
        requestId: requestIdFor(res),
        route: classname,
        identity: auth?.session?.user?.email ? "session" : "ip",
        target: sanitizeRequestTarget(req.originalUrl || req.url),
        retryAfter: result.retryAfter,
      });
      res.status(429).json({ error: "RATE_LIMITED", retryAfter: result.retryAfter });
      return;
    }
    next();
  };
}

export function notePayloadRejected(
  req: Request,
  res: Response,
  route: string,
  limitBytes: number
): void {
  counters.payloadRejected += 1;
  logEvent("warn", "http.payload_rejected", {
    requestId: requestIdFor(res),
    route,
    target: sanitizeRequestTarget(req.originalUrl || req.url),
    limitBytes,
    contentLength: req.get("content-length") || "unknown",
  });
  if (!res.headersSent) {
    res.status(413).json({ error: "PAYLOAD_TOO_LARGE", limitBytes });
  }
}

export function noteAuthFailure(req: Request, res: Response, reason: string): void {
  counters.authFailures += 1;
  logEvent("warn", "auth.failure", {
    requestId: requestIdFor(res),
    reason,
    target: sanitizeRequestTarget(req.originalUrl || req.url),
    ip: clientAddress(req),
  });
}

export function noteProxyError(req: Request, res: Response, error: unknown): void {
  counters.proxyErrors += 1;
  logEvent("error", "wings.proxy_error", {
    requestId: requestIdFor(res),
    target: sanitizeRequestTarget(req.originalUrl || req.url),
    message: error instanceof Error ? error.message : String(error),
  });
}

export function snapshotRuntimeMetrics() {
  return {
    version: RUNTIME_VERSION,
    startedAt: new Date(counters.startedAt).toISOString(),
    uptimeSeconds: Math.round((Date.now() - counters.startedAt) / 1000),
    requests: counters.requests,
    activeRequests: counters.activeRequests,
    responses: {
      "2xx": counters.responses2xx,
      "3xx": counters.responses3xx,
      "4xx": counters.responses4xx,
      "5xx": counters.responses5xx,
    },
    throttled: counters.throttled,
    payloadRejected: counters.payloadRejected,
    authFailures: counters.authFailures,
    proxyErrors: counters.proxyErrors,
    auditFailures: counters.auditFailures,
    logDirConfigured: Boolean(logDir),
    auditDirConfigured: Boolean(auditDir),
  };
}

export function readAuditRecords(query: AuditQuery = {}): AuditRecord[] {
  const limit = Math.min(Math.max(Number(query.limit) || 100, 1), 50_000);
  const records: AuditRecord[] = [];
  const files = auditFilesNewestFirst();
  for (const file of files) {
    let contents = "";
    try {
      contents = fs.readFileSync(file, "utf8");
    } catch {
      continue;
    }
    const lines = contents.split("\n");
    for (let index = lines.length - 1; index >= 0; index -= 1) {
      const line = lines[index].trim();
      if (!line) continue;
      let record: AuditRecord;
      try {
        record = JSON.parse(line) as AuditRecord;
      } catch {
        continue;
      }
      if (query.actor && record.actor !== query.actor) continue;
      if (query.action && record.action !== query.action) continue;
      if (query.result && record.result !== query.result) continue;
      if (query.requestId && record.requestId !== query.requestId) continue;
      if (query.since && record.epochMs < query.since) continue;
      if (query.until && record.epochMs > query.until) continue;
      records.push(record);
      if (records.length >= limit) return records;
    }
  }
  return records;
}

export function auditFilesNewestFirst(): string[] {
  if (!auditDir) return [];
  try {
    return fs
      .readdirSync(auditDir)
      .filter((name) => name.startsWith("audit-") && name.endsWith(".jsonl"))
      .sort()
      .reverse()
      .map((name) => path.join(auditDir, name));
  } catch {
    return [];
  }
}

export function auditDirectory(): string {
  return auditDir;
}
