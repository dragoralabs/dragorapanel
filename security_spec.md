# Security Specification (`security_spec.md`)

## 1. Data Invariants
1. **Default-Deny Catch-All**: Any path not explicitly matched in `/databases/{database}/documents` is unconditionally denied (`allow read, write: if false;`).
2. **ID Hardening**: All document IDs (`{adminId}`, `{userId}`, `{configId}`) must match `^[a-zA-Z0-9_\-]+$` and have length `1..128`.
3. **Zero Trust RBAC**: Administrator access (`isAdmin()`) requires `request.auth != null`, `request.auth.token.email_verified == true`, and either a verified owner email (`dragoralabs@gmail.com`) or existence of `/admins/$(request.auth.uid)`.
4. **Per-User Isolation (`/user_configs/{userId}`)**:
   - Contains zero PII (only `ownerUid`, `displayName`, and hardware node preferences).
   - Only the authenticated owner (`request.auth.uid == userId`) or an admin can `get`, `create`, `update`, or `delete` their `/user_configs/{userId}` document.
   - `ownerUid` must equal `request.auth.uid` and `userId`, and `createdAt` + `ownerUid` are immutable on update.
5. **Strict Schema & Bounds**: Every string field is bounded by `.size() >= min && .size() <= max`, every numeric field (`customRam`) is bounded (`4..512`), and every array field (`laws`, `osImages`, `savedManifests`) is bounded to `<= 20` items.
6. **Temporal Integrity**: `createdAt == request.time` on `create`, and `createdAt == existing().createdAt && updatedAt == request.time` on `update`.

## 2. The "Dirty Dozen" Adversarial Payloads
1. **Unauthenticated Write to `/site_config/main`**: `auth = null` attempting `create` -> `PERMISSION_DENIED`.
2. **Non-Admin Authenticated Write to `/site_config/main`**: `auth.uid = 'attacker_1'` attempting `update` on `/site_config/main` -> `PERMISSION_DENIED`.
3. **Cross-User Read on `/user_configs/{userId}`**: `auth.uid = 'user_b'` attempting `get` on `/user_configs/user_a` -> `PERMISSION_DENIED`.
4. **Identity Spoofing on `/user_configs/{userId}` Create**: `auth.uid = 'user_a'` attempting `create` with `ownerUid: 'user_b'` -> `PERMISSION_DENIED`.
5. **Shadow Field Injection on `/user_configs/{userId}` Update**: `auth.uid = 'user_a'` attempting `update` with extra field `isAdmin: true` -> `PERMISSION_DENIED`.
6. **Immutable `ownerUid` Mutation on `/user_configs/{userId}` Update**: `auth.uid = 'user_a'` attempting to change `ownerUid` -> `PERMISSION_DENIED`.
7. **Immutable `createdAt` Tampering on `/site_config/main` Update**: Admin attempting to overwrite `createdAt` -> `PERMISSION_DENIED`.
8. **Future/Past Forged `updatedAt` Timestamp**: Client passing forged timestamp instead of `request.time` -> `PERMISSION_DENIED`.
9. **Oversized String Payload (Denial of Wallet)**: Client passing 5,000-character string in `heroDescription` or `selectedEngine` -> `PERMISSION_DENIED`.
10. **Unbounded Array Exhaustion**: Client passing 50 items in `savedManifests` or `laws` -> `PERMISSION_DENIED`.
11. **Invalid Enum Value**: Client passing `theme: 'hacked'` or `billingCycle: 'lifetime'` -> `PERMISSION_DENIED`.
12. **Self-Assigned Admin Escalation**: Non-admin user attempting to create `/admins/attacker_1` -> `PERMISSION_DENIED`.
