import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

test.describe('Case management (LGU)', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page, USERS.lgu_official);
  });

  test('cases page shows summary and case list', async ({ page }) => {
    await expect(page.locator('.feed-title')).toContainText(/case management/i);
    await expect(page.locator('.summary-strip, .case-list, .card').first()).toBeVisible();
  });

  test('status filter updates results', async ({ page }) => {
    await page.locator('select[name="status"]').selectOption('Received');
    await page.waitForURL(/status=Received/);
    await expect(page.locator('select[name="status"]')).toHaveValue('Received');
  });
});

test.describe('Admin console', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page, USERS.admin);
  });

  test('admin dashboard shows pending sections', async ({ page }) => {
    await expect(page.locator('.feed-title')).toContainText(/admin console/i);
    await expect(page.getByText(/pending account approvals/i)).toBeVisible();
    await expect(page.getByText(/pending dog registrations/i)).toBeVisible();
  });
});
