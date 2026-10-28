/**
 * Firestore Security Rules Verification Suite (Dirty Dozen Payloads)
 * Verifies all 12 adversarial payloads in security_spec.md are rejected with PERMISSION_DENIED.
 */

export interface AdversarialTestCase {
  id: number;
  name: string;
  operation: 'get' | 'list' | 'create' | 'update' | 'delete';
  path: string;
  auth: { uid: string; email?: string; email_verified?: boolean } | null;
  payload?: Record<string, unknown>;
  expected: 'PERMISSION_DENIED' | 'ALLOWED';
}

export const DIRTY_DOZEN_TESTS: AdversarialTestCase[] = [
  {
    id: 1,
    name: 'Unauthenticated write to site_config/main',
    operation: 'create',
    path: '/site_config/main',
    auth: null,
    payload: { brandMain: 'play' },
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 2,
    name: 'Unverified admin email spoof',
    operation: 'update',
    path: '/site_config/main',
    auth: { uid: 'spoof1', email: 'no3035564@gmail.com', email_verified: false },
    payload: { brandMain: 'hacked' },
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 3,
    name: 'Non-admin authenticated write',
    operation: 'update',
    path: '/site_config/main',
    auth: { uid: 'user1', email: 'user@example.com', email_verified: true },
    payload: { brandMain: 'hacked' },
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 4,
    name: 'Shadow field injection on site_config/main',
    operation: 'update',
    path: '/site_config/main',
    auth: { uid: 'admin1', email: 'no3035564@gmail.com', email_verified: true },
    payload: { brandMain: 'play', shadowField: true },
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 5,
    name: 'Author UID spoofing on create',
    operation: 'create',
    path: '/site_config/main',
    auth: { uid: 'admin1', email: 'no3035564@gmail.com', email_verified: true },
    payload: { authorUid: 'other_uid' },
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 6,
    name: 'Immutable createdAt tampering on update',
    operation: 'update',
    path: '/site_config/main',
    auth: { uid: 'admin1', email: 'no3035564@gmail.com', email_verified: true },
    payload: { createdAt: '2020-01-01T00:00:00Z' },
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 7,
    name: 'Client timestamp forgery on update',
    operation: 'update',
    path: '/site_config/main',
    auth: { uid: 'admin1', email: 'no3035564@gmail.com', email_verified: true },
    payload: { updatedAt: '2099-01-01T00:00:00Z' },
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 8,
    name: 'Value poisoning oversized heroKicker (>400 chars)',
    operation: 'update',
    path: '/site_config/main',
    auth: { uid: 'admin1', email: 'no3035564@gmail.com', email_verified: true },
    payload: { heroKicker: 'A'.repeat(450) },
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 9,
    name: 'Array exhaustion laws (>20 items)',
    operation: 'update',
    path: '/site_config/main',
    auth: { uid: 'admin1', email: 'no3035564@gmail.com', email_verified: true },
    payload: { laws: Array.from({ length: 25 }, (_, i) => `Law ${i}`) },
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 10,
    name: 'Invalid enum theme value',
    operation: 'update',
    path: '/site_config/main',
    auth: { uid: 'admin1', email: 'no3035564@gmail.com', email_verified: true },
    payload: { theme: 'invalid_theme' },
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 11,
    name: 'Path ID poisoning on site_config',
    operation: 'get',
    path: '/site_config/bad$id!',
    auth: null,
    expected: 'PERMISSION_DENIED',
  },
  {
    id: 12,
    name: 'Unauthorized admin self-promotion',
    operation: 'create',
    path: '/admins/user1',
    auth: { uid: 'user1', email: 'user@example.com', email_verified: true },
    payload: { uid: 'user1', role: 'super_admin' },
    expected: 'PERMISSION_DENIED',
  },
];
