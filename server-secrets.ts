import crypto from "node:crypto";
import fs from "node:fs";
import os from "node:os";
import path from "node:path";

const ENCRYPTED_PREFIX = "enc:v1:";
const KEY_BYTES = 32;
const IV_BYTES = 12;

let secretKey: Buffer | null = null;
let keySource: "env" | "file" | "generated" | null = null;
let configuredKeyFile = "";

export interface SecretsOptions {
  keyFile?: string;
}

export function configureSecrets(options: SecretsOptions = {}): void {
  if (secretKey) return;
  const envKey = String(process.env.PANEL_SECRET_KEY || "").trim();
  if (envKey) {
    secretKey = parseKey(envKey, "PANEL_SECRET_KEY");
    keySource = "env";
    return;
  }
  const keyFile =
    options.keyFile ||
    process.env.PANEL_SECRET_KEY_FILE ||
    path.join(os.homedir(), ".playit", "panel-secret.key");
  configuredKeyFile = path.resolve(keyFile);
  try {
    const existing = fs.readFileSync(configuredKeyFile, "utf8").trim();
    if (existing) {
      secretKey = parseKey(existing, "PANEL_SECRET_KEY_FILE");
      keySource = "file";
      return;
    }
  } catch (error) {
    if ((error as NodeJS.ErrnoException).code !== "ENOENT") throw error;
  }
  const generated = crypto.randomBytes(KEY_BYTES).toString("hex");
  fs.mkdirSync(path.dirname(configuredKeyFile), { recursive: true, mode: 0o700 });
  fs.writeFileSync(configuredKeyFile, `${generated}\n`, { mode: 0o600 });
  try {
    fs.chmodSync(configuredKeyFile, 0o600);
  } catch {
    // Windows ACLs are managed outside POSIX mode bits.
  }
  secretKey = Buffer.from(generated, "hex");
  keySource = "generated";
  console.warn(
    `Generated a panel secret key at ${configuredKeyFile}. Set PANEL_SECRET_KEY or back this file up; losing it makes encrypted settings unreadable.`
  );
}

export function secretsStatus() {
  return {
    configured: secretKey !== null,
    source: keySource,
    keyFileConfigured: Boolean(configuredKeyFile),
  };
}

export function isEncryptedValue(value: unknown): boolean {
  return typeof value === "string" && value.startsWith(ENCRYPTED_PREFIX);
}

export function encryptValue(plaintext: string): string {
  if (!plaintext || isEncryptedValue(plaintext)) return plaintext;
  const key = requireKey();
  const iv = crypto.randomBytes(IV_BYTES);
  const cipher = crypto.createCipheriv("aes-256-gcm", key, iv);
  const ciphertext = Buffer.concat([cipher.update(plaintext, "utf8"), cipher.final()]);
  const tag = cipher.getAuthTag();
  return `${ENCRYPTED_PREFIX}${iv.toString("base64url")}.${tag.toString(
    "base64url"
  )}.${ciphertext.toString("base64url")}`;
}

export function decryptValue(value: string): string {
  if (!isEncryptedValue(value)) return value;
  const key = requireKey();
  const parts = value.slice(ENCRYPTED_PREFIX.length).split(".");
  if (parts.length !== 3) throw new Error("SECRET_DECRYPT_FAILED: malformed ciphertext");
  try {
    const iv = Buffer.from(parts[0], "base64url");
    const tag = Buffer.from(parts[1], "base64url");
    const ciphertext = Buffer.from(parts[2], "base64url");
    if (iv.length !== IV_BYTES || tag.length !== 16) {
      throw new Error("invalid nonce or tag");
    }
    const decipher = crypto.createDecipheriv("aes-256-gcm", key, iv);
    decipher.setAuthTag(tag);
    return Buffer.concat([decipher.update(ciphertext), decipher.final()]).toString("utf8");
  } catch {
    throw new Error("SECRET_DECRYPT_FAILED: panel secret key does not match stored data");
  }
}

export function encryptSensitive<T>(value: T): T {
  return transformSensitive(value, "encrypt") as T;
}

export function decryptSensitive<T>(value: T): T {
  return transformSensitive(value, "decrypt") as T;
}

export function isSensitiveField(key: string): boolean {
  const normalized = key.toLowerCase();
  if (
    normalized === "passwordhash" ||
    normalized === "adminpinhash" ||
    normalized === "tokenhash" ||
    normalized === "sitekey" ||
    normalized === "publickey" ||
    normalized === "keyid"
  ) {
    return false;
  }
  return (
    normalized === "password" ||
    normalized === "secretkey" ||
    normalized === "token" ||
    normalized === "apikey" ||
    normalized === "accesskey" ||
    normalized === "secretaccesskey" ||
    normalized === "agenttoken" ||
    normalized === "privatekey" ||
    normalized === "credentials" ||
    normalized === "authorization" ||
    normalized === "smtppassword" ||
    normalized.endsWith("password") ||
    normalized.endsWith("secret") ||
    normalized.endsWith("token") ||
    normalized.endsWith("apikey") ||
    normalized.endsWith("accesskey") ||
    normalized.endsWith("privatekey")
  );
}

function transformSensitive(value: unknown, mode: "encrypt" | "decrypt"): unknown {
  if (Array.isArray(value)) return value.map((item) => transformSensitive(item, mode));
  if (!value || typeof value !== "object") return value;
  const output: Record<string, unknown> = {};
  for (const [key, entry] of Object.entries(value as Record<string, unknown>)) {
    if (typeof entry === "string" && isSensitiveField(key)) {
      output[key] = mode === "encrypt" ? encryptValue(entry) : decryptValue(entry);
    } else if (entry && typeof entry === "object") {
      output[key] = transformSensitive(entry, mode);
    } else {
      output[key] = entry;
    }
  }
  return output;
}

function requireKey(): Buffer {
  if (!secretKey) throw new Error("SECRET_KEY_NOT_CONFIGURED");
  return secretKey;
}

function parseKey(raw: string, source: string): Buffer {
  const trimmed = raw.trim();
  if (/^[0-9a-fA-F]{64}$/.test(trimmed)) return Buffer.from(trimmed, "hex");
  try {
    const decoded = Buffer.from(trimmed, "base64url");
    if (decoded.length === KEY_BYTES) return decoded;
  } catch {
    // Fall through to the explicit error below.
  }
  throw new Error(`${source} must contain exactly 32 bytes (64 hex or base64url characters)`);
}
