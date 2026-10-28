import { expect, test } from '@playwright/test';
import { E2E_ADMIN_EMAIL, E2E_ADMIN_PASSWORD } from './credentials';

async function loginOwner(request: import('@playwright/test').APIRequestContext) {
  const response = await request.post('/api/auth/login', {
    data: { email: E2E_ADMIN_EMAIL, password: E2E_ADMIN_PASSWORD },
  });
  expect(response.status()).toBe(200);
  const body = await response.json();
  expect(body.user).toMatchObject({ email: E2E_ADMIN_EMAIL, role: 'super_admin' });
}

async function copySessionToBrowser(
  request: import('@playwright/test').APIRequestContext,
  page: import('@playwright/test').Page
) {
  const sessionCookies = (await request.storageState()).cookies.filter(
    (cookie) => cookie.name === 'playit_session'
  );
  expect(sessionCookies.length).toBeGreaterThan(0);
  await page.context().addCookies(sessionCookies);
}

test.describe('public surface', () => {
  test('storefront renders without uncaught errors or overflow', async ({ page }) => {
    const errors: string[] = [];
    page.on('pageerror', (error) => errors.push(error.message));
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('body')).toContainText(/playit/i);
    await expect(page.locator('nav, header').first()).toBeVisible();
    const overflows = await page.evaluate(
      () => document.documentElement.scrollWidth > window.innerWidth + 1
    );
    expect(overflows).toBe(false);
    expect(errors).toEqual([]);
  });

  test('mobile storefront stays inside the viewport', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('body')).toContainText(/playit/i);
    const overflows = await page.evaluate(
      () => document.documentElement.scrollWidth > window.innerWidth + 1
    );
    expect(overflows).toBe(false);
  });
});

test.describe('access gates', () => {
  test('panel asks anonymous visitors to authenticate', async ({ page }) => {
    await page.goto('/panel');
    await expect(page).toHaveURL(/\/panel$/);
    await expect(page.locator('input[type="password"]')).toBeVisible();
    await expect(page.locator('body')).toContainText(/server control panel|email or username|login to panel/i);
  });

  test('admin area redirects anonymous visitors to the panel login', async ({ page }) => {
    await page.goto('/panel/admin');
    await expect(page).toHaveURL(/\/panel$/);
    await expect(page.locator('input[type="password"]')).toBeVisible();
  });

  test('public /admin never exposes the dashboard to anonymous visitors', async ({ page }) => {
    await page.goto('/admin');
    await expect(page.locator('body')).not.toContainText(/administrator control|staff management/i);
    await expect(
      page.locator('input[type="email"], input[type="password"]').first()
    ).toBeVisible();
  });

  test('server-rendered session unlocks the panel after login', async ({ page, request }) => {
    await loginOwner(request);
    await copySessionToBrowser(request, page);
    await page.goto('/panel');
    await expect(page.locator('body')).toContainText(/panel|dashboard|server/i);
    await expect(page.locator('body')).not.toContainText(/login to panel|solve captcha/i);
  });
});

test.describe('API authorization', () => {
  test('anonymous requests cannot read admin settings or staff records', async ({ request }) => {
    for (const endpoint of [
      '/api/settings/admin',
      '/api/staff',
      '/api/analytics',
      '/api/system/hardware',
      '/api/system/cluster-telemetry',
    ]) {
      const response = await request.get(endpoint);
      expect(response.status(), endpoint).toBe(401);
      expect(response.headers()['content-type']).toContain('application/json');
    }
  });

  test('anonymous requests cannot mutate staff or settings', async ({ request }) => {
    const staff = await request.post('/api/staff', {
      data: { email: 'intruder@example.com', name: 'Intruder', role: 'super_admin' },
    });
    expect(staff.status()).toBe(401);

    const settings = await request.post('/api/settings', {
      data: { copyText: 'hijacked.example' },
    });
    expect(settings.status()).toBe(401);
  });

  test('public settings expose no PIN material or infrastructure secrets', async ({ request }) => {
    const response = await request.get('/api/settings');
    expect(response.status()).toBe(200);
    const body = await response.json();
    expect(body).not.toHaveProperty('securityConfig.adminPinHash');
    expect(body).not.toHaveProperty('securityConfig.adminPinCode');
    expect(body).not.toHaveProperty('smtpConfig.password');
    expect(body.apiKeys).toEqual([]);
    expect(body.nodeHosts).toEqual([]);
    expect(body.databaseHosts).toEqual([]);
  });

  test('unknown API routes return JSON 404 instead of the SPA shell', async ({ request }) => {
    const response = await request.get('/api/definitely-not-a-route');
    expect(response.status()).toBe(404);
    expect(response.headers()['content-type']).toContain('application/json');
    expect(await response.json()).toMatchObject({ error: 'API_ROUTE_NOT_FOUND' });
  });

  test('cross-origin requests are rejected even with a valid session', async ({ request }) => {
    const anonymous = await request.get('/api/settings', {
      headers: { Origin: 'http://evil.example' },
    });
    expect(anonymous.status()).toBe(403);

    await loginOwner(request);
    const authenticated = await request.get('/api/settings/admin', {
      headers: { Origin: 'http://evil.example' },
    });
    expect(authenticated.status()).toBe(403);
    expect(authenticated.headers()['access-control-allow-origin']).toBeUndefined();
  });

  test('bad credentials are rejected and good credentials issue an HttpOnly session', async ({ request }) => {
    const bad = await request.post('/api/auth/login', {
      data: { email: E2E_ADMIN_EMAIL, password: 'definitely-wrong-password' },
    });
    expect(bad.status()).toBe(401);

    const good = await request.post('/api/auth/login', {
      data: { email: E2E_ADMIN_EMAIL, password: E2E_ADMIN_PASSWORD },
    });
    expect(good.status()).toBe(200);
    const sessionCookie = (await request.storageState()).cookies.find(
      (cookie) => cookie.name === 'playit_session'
    );
    expect(sessionCookie).toMatchObject({
      httpOnly: true,
      sameSite: 'Strict',
      path: '/',
    });

    const session = await request.get('/api/auth/session');
    expect(await session.json()).toMatchObject({
      authenticated: true,
      user: { email: E2E_ADMIN_EMAIL, role: 'super_admin' },
    });

    await request.post('/api/auth/logout');
    const closed = await request.get('/api/auth/session');
    expect(await closed.json()).toMatchObject({ authenticated: false, user: null });
  });

  test('ordinary staff cannot read or mutate administrator resources', async ({ request }) => {
    await loginOwner(request);
    const created = await request.post('/api/staff', {
      data: {
        email: 'smoke-staff@example.com',
        name: 'Smoke Staff',
        role: 'staff',
        password: 'smoke-staff-password-123',
      },
    });
    expect(created.status()).toBe(200);
    await request.post('/api/auth/logout');

    const staffLogin = await request.post('/api/auth/login', {
      data: { email: 'smoke-staff@example.com', password: 'smoke-staff-password-123' },
    });
    expect(staffLogin.status()).toBe(200);

    expect((await request.get('/api/settings/admin')).status()).toBe(403);
    expect((await request.get('/api/staff')).status()).toBe(403);
    expect((await request.get('/api/analytics')).status()).toBe(403);
    expect(
      (
        await request.post('/api/staff', {
          data: { email: 'escalation@example.com', role: 'super_admin' },
        })
      ).status()
    ).toBe(403);

    await request.post('/api/auth/logout');
    await loginOwner(request);
    await request.delete('/api/staff/smoke-staff@example.com');
  });

  test('the 404 PIN gate verifies server-side and never exposes the PIN', async ({ request }) => {
    await loginOwner(request);
    const enable = await request.post('/api/security/update-pin', {
      data: { newPin: '739204', pinGateEnabled: true },
    });
    expect(enable.status()).toBe(200);
    const enabledBody = await enable.json();
    expect(enabledBody.securityConfig).toMatchObject({ pinGateEnabled: true, pinSet: true });
    expect(JSON.stringify(enabledBody)).not.toContain('739204');

    const publicSettings = await request.get('/api/settings');
    const publicBody = await publicSettings.json();
    expect(publicBody.securityConfig).toMatchObject({ pinGateEnabled: true, pinSet: true });
    expect(JSON.stringify(publicBody)).not.toContain('739204');
    expect(publicBody.securityConfig).not.toHaveProperty('adminPinHash');

    const wrong = await request.post('/api/security/verify-pin', { data: { pin: '000000' } });
    expect(wrong.status()).toBe(401);

    const unlocked = await request.post('/api/security/verify-pin', { data: { pin: '739204' } });
    expect(unlocked.status()).toBe(200);
    expect(await unlocked.json()).toMatchObject({ pinUnlocked: true });

    await request.post('/api/security/lock');
    const lockedSession = await request.get('/api/auth/session');
    expect(await lockedSession.json()).toMatchObject({ authenticated: true, pinUnlocked: false });
  });
});

test.describe('Wings containment', () => {
  test('anonymous Wings proxy access is rejected', async ({ request }) => {
    const health = await request.get('/wings/health');
    expect(health.status()).toBe(401);
    expect(await health.json()).toMatchObject({ error: 'AUTHENTICATION_REQUIRED' });
  });

  test('same-origin proxy reaches Wings and injects the daemon token after login', async ({ request }) => {
    await loginOwner(request);
    const health = await request.get('/wings/health');
    expect(health.status()).toBe(200);
    const healthBody = await health.json();
    expect(healthBody).toMatchObject({ ok: true });
    expect(Array.isArray(healthBody.queue.bypass)).toBe(true);

    // An empty queue must serialize as [] — null crashed the Node System page.
    const queue = await request.get('/wings/queue');
    expect(queue.status()).toBe(200);
    expect(Array.isArray((await queue.json()).bypass)).toBe(true);

    // Wings returns 401 for /containers without its token; the proxy must add it.
    const containers = await request.get('/wings/containers');
    expect(containers.status()).toBe(200);
    expect(await containers.json()).toHaveProperty('containers');

    const info = await request.get('/playit-api/api/node/info');
    expect(info.status()).toBe(200);
  });

  test('direct daemon access without the token is rejected', async ({ request }) => {
    const direct = await request.get('http://127.0.0.1:8091/containers');
    expect(direct.status()).toBe(401);
  });

  test('cross-origin Wings reads and preflights are blocked', async ({ request }) => {
    const read = await request.get('/wings/containers', {
      headers: { Origin: 'http://evil.example' },
    });
    expect(read.status()).toBe(403);

    const preflight = await request.fetch('/wings/containers', {
      method: 'OPTIONS',
      headers: {
        Origin: 'http://evil.example',
        'Access-Control-Request-Method': 'POST',
      },
    });
    expect(preflight.status()).toBe(403);
    expect(preflight.headers()['access-control-allow-origin']).toBeUndefined();
  });
});
