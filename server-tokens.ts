import crypto from "node:crypto";
import fs from "node:fs";
import path from "node:path";
import { decryptSensitive, encryptSensitive } from "./server-secrets";

export interface NodeTokenMeta {
  id: string;
  nodeId: string;
  createdAt: string;
  expiresAt: string | null;
  revokedAt: string | null;
  lastUsedAt: string | null;
  active: boolean;
}

interface StoredToken {
  id: string;
  nodeId: string;
  secret: string;
  createdAt: string;
  expiresAt: string | null;
  revokedAt: string | null;
}

interface NodeRecord {
  activeId: string;
  tokens: StoredToken[];
}

interface TokenStore {
  version: 1;
  nodes: Record<string, NodeRecord>;
}

let storeFile = "";
let wingsTokensFile = "";
let localNodeId = "local";
let store: TokenStore = { version: 1, nodes: {} };

export function configureNodeTokens(options: {
  dataDir: string;
  nodeId?: string;
  wingsTokensFile?: string;
}): void {
  localNodeId = sanitizeNodeId(options.nodeId || process.env.PANEL_LOCAL_NODE_ID || "local");
  const dir = path.join(options.dataDir, "security");
  fs.mkdirSync(dir, { recursive: true, mode: 0o700 });
  storeFile = path.join(dir, "node-tokens.json");
  wingsTokensFile =
    options.wingsTokensFile || String(process.env.WINGS_TOKENS_FILE || "").trim();
  loadTokenStore();
}

export function localNode(): string {
  return localNodeId;
}

export function activeNodeSecret(nodeId = localNodeId): string | null {
  const record = store.nodes[nodeId];
  if (!record?.activeId) return null;
  const token = record.tokens.find((candidate) => candidate.id === record.activeId);
  if (!token || !isUsable(token)) return null;
  return token.secret;
}

export function rotateNodeToken(
  nodeId = localNodeId,
  graceMs = 24 * 60 * 60 * 1000
): { secret: string; token: NodeTokenMeta } {
  const normalized = sanitizeNodeId(nodeId);
  const now = Date.now();
  const record = store.nodes[normalized] || { activeId: "", tokens: [] };
  for (const token of record.tokens) {
    if (token.id === record.activeId && !token.expiresAt) {
      token.expiresAt = new Date(now + Math.max(graceMs, 60_000)).toISOString();
    }
  }
  record.tokens = record.tokens.filter(
    (token) =>
      !token.revokedAt &&
      (!token.expiresAt || Date.parse(token.expiresAt) > now - 24 * 60 * 60 * 1000)
  );
  const secret = `pwt_${normalized}_${crypto.randomBytes(32).toString("base64url")}`;
  const token: StoredToken = {
    id: `tok_${crypto.randomUUID()}`,
    nodeId: normalized,
    secret,
    createdAt: new Date(now).toISOString(),
    expiresAt: null,
    revokedAt: null,
  };
  record.tokens.push(token);
  record.activeId = token.id;
  store.nodes[normalized] = record;
  persistTokenStore();
  syncWingsTokenFile(normalized);
  return { secret, token: toMeta(record, token) };
}

export function revokeNodeToken(nodeId: string, tokenId: string): boolean {
  const normalized = sanitizeNodeId(nodeId);
  const record = store.nodes[normalized];
  if (!record) return false;
  const token = record.tokens.find((candidate) => candidate.id === tokenId);
  if (!token || token.revokedAt) return false;
  token.revokedAt = new Date().toISOString();
  token.expiresAt = token.expiresAt || new Date().toISOString();
  if (record.activeId === token.id) record.activeId = "";
  persistTokenStore();
  syncWingsTokenFile(normalized);
  return true;
}

export function listNodeTokens(nodeId?: string): NodeTokenMeta[] {
  const usage = readWingsTokenUsage();
  const nodes = nodeId
    ? [sanitizeNodeId(nodeId)]
    : Object.keys(store.nodes).sort();
  const output: NodeTokenMeta[] = [];
  for (const key of nodes) {
    const record = store.nodes[key];
    if (!record) continue;
    for (const token of record.tokens) {
      const meta = toMeta(record, token);
      meta.lastUsedAt = usage[token.id] || null;
      output.push(meta);
    }
  }
  return output.sort((left, right) => right.createdAt.localeCompare(left.createdAt));
}

export function tokensStatus() {
  return {
    nodeId: localNodeId,
    storeConfigured: Boolean(storeFile),
    wingsFileConfigured: Boolean(wingsTokensFile),
    nodes: Object.keys(store.nodes).length,
    activeTokens: Object.values(store.nodes).filter((record) => {
      const active = record.tokens.find((token) => token.id === record.activeId);
      return Boolean(active && isUsable(active));
    }).length,
  };
}

function loadTokenStore(): void {
  if (!storeFile || !fs.existsSync(storeFile)) {
    store = { version: 1, nodes: {} };
    return;
  }
  try {
    const parsed = JSON.parse(fs.readFileSync(storeFile, "utf8")) as TokenStore;
    const decrypted = decryptSensitive(parsed);
    if (!decrypted || decrypted.version !== 1 || typeof decrypted.nodes !== "object") {
      throw new Error("invalid token store shape");
    }
    store = decrypted;
  } catch (error) {
    throw new Error(
      `NODE_TOKEN_STORE_UNREADABLE: ${
        error instanceof Error ? error.message : String(error)
      }`
    );
  }
}

function persistTokenStore(): void {
  if (!storeFile) throw new Error("NODE_TOKEN_STORE_NOT_CONFIGURED");
  const encrypted = encryptSensitive(store);
  const tmp = `${storeFile}.tmp`;
  fs.writeFileSync(tmp, `${JSON.stringify(encrypted, null, 2)}\n`, { mode: 0o600 });
  fs.renameSync(tmp, storeFile);
  try {
    fs.chmodSync(storeFile, 0o600);
  } catch {
    // Windows ACLs are managed outside POSIX mode bits.
  }
}

function syncWingsTokenFile(nodeId: string): void {
  if (!wingsTokensFile) return;
  const record = store.nodes[nodeId];
  const tokens = (record?.tokens || [])
    .filter((token) => isUsable(token))
    .map((token) => ({
      id: token.id,
      nodeId: token.nodeId,
      sha256: crypto.createHash("sha256").update(token.secret).digest("hex"),
      createdAt: token.createdAt,
      expiresAt: token.expiresAt,
    }));
  const target = path.resolve(wingsTokensFile);
  fs.mkdirSync(path.dirname(target), { recursive: true, mode: 0o700 });
  const tmp = `${target}.tmp`;
  fs.writeFileSync(tmp, `${JSON.stringify({ version: 1, tokens }, null, 2)}\n`, {
    mode: 0o600,
  });
  fs.renameSync(tmp, target);
  try {
    fs.chmodSync(target, 0o600);
  } catch {
    // Windows ACLs are managed outside POSIX mode bits.
  }
}

function readWingsTokenUsage(): Record<string, string> {
  if (!wingsTokensFile) return {};
  try {
    const raw = JSON.parse(fs.readFileSync(`${wingsTokensFile}.usage.json`, "utf8")) as {
      tokens?: Record<string, { lastUsedAt?: string }>;
    };
    const output: Record<string, string> = {};
    for (const [id, value] of Object.entries(raw.tokens || {})) {
      if (value?.lastUsedAt) output[id] = value.lastUsedAt;
    }
    return output;
  } catch {
    return {};
  }
}

function toMeta(record: NodeRecord, token: StoredToken): NodeTokenMeta {
  return {
    id: token.id,
    nodeId: token.nodeId,
    createdAt: token.createdAt,
    expiresAt: token.expiresAt,
    revokedAt: token.revokedAt,
    lastUsedAt: null,
    active: record.activeId === token.id && isUsable(token),
  };
}

function isUsable(token: StoredToken): boolean {
  if (token.revokedAt) return false;
  if (token.expiresAt && Date.parse(token.expiresAt) <= Date.now()) return false;
  return true;
}

function sanitizeNodeId(raw: string): string {
  const value = String(raw || "").trim();
  if (!/^[A-Za-z0-9_-]{1,64}$/.test(value)) {
    throw new Error("INVALID_NODE_ID");
  }
  return value;
}
