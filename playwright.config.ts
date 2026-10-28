import path from 'node:path';
import { defineConfig } from '@playwright/test';
import {
  E2E_ADMIN_EMAIL,
  E2E_ADMIN_PASSWORD,
  E2E_WINGS_TOKEN,
} from './e2e/credentials';

const root = process.cwd();
const wingsBinary = path.join(
  root,
  'node-wings',
  process.platform === 'win32' ? 'node-wings.exe' : 'node-wings'
);
const runDir = path.join(root, '.tmp', `e2e-${Date.now()}`);

export default defineConfig({
  testDir: './e2e',
  fullyParallel: false,
  workers: 1,
  retries: process.env.CI ? 1 : 0,
  timeout: 45000,
  expect: { timeout: 12000 },
  reporter: process.env.CI ? [['list'], ['html', { open: 'never' }]] : 'list',
  use: {
    baseURL: 'http://127.0.0.1:3100',
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    ...(process.env.CI ? {} : { channel: 'chrome' }),
  },
  webServer: [
    {
      command: `"${wingsBinary}"`,
      url: 'http://127.0.0.1:8091/health',
      reuseExistingServer: false,
      timeout: 60000,
      env: {
        ...process.env,
        PORT: '8091',
        BIND_ADDR: '127.0.0.1',
        AGENT_TOKEN: E2E_WINGS_TOKEN,
        DATA_DIR: path.join(runDir, 'wings'),
      },
    },
    {
      command: 'npm run build && npx tsx server.ts',
      url: 'http://127.0.0.1:3100',
      reuseExistingServer: false,
      timeout: 180000,
      env: {
        ...process.env,
        NODE_ENV: 'production',
        PORT: '3100',
        NODE_AGENT_URL: 'http://127.0.0.1:8091',
        NODE_AGENT_TOKEN: E2E_WINGS_TOKEN,
        PANEL_DATA_DIR: path.join(runDir, 'panel'),
        PANEL_ADMIN_EMAIL: E2E_ADMIN_EMAIL,
        PANEL_ADMIN_PASSWORD: E2E_ADMIN_PASSWORD,
        PANEL_COOKIE_SECURE: '0',
      },
    },
  ],
});
