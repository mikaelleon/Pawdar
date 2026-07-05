import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

test.describe('Report incident drawer', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page, USERS.community_reporter);
    await page.goto('/feed.php');
  });

  test('opens drawer from feed action', async ({ page }) => {
    await page.locator('[data-open-report-drawer]').first().click();
    await expect(page.locator('[data-report-drawer]')).toHaveClass(/is-open/);
    await expect(page.locator('[data-report-form]')).toBeVisible();
  });

  test('validates location on step 2 before advancing', async ({ page }) => {
    await page.locator('[data-open-report-drawer]').first().click();
    await page.locator('[data-report-next]').first().click();
    await page.locator('[data-report-next]').first().click();
    await expect(page.locator('[data-report-step="2"]')).toBeVisible();
    await expect(page.locator('#report-location')).toHaveValue('');
  });

  test('submits a new incident through all steps', async ({ page }) => {
    const uniqueLocation = `Playwright Test Location ${Date.now()}`;

    await page.locator('[data-open-report-drawer]').first().click();
    await page.locator('[data-report-next]').first().click();
    await page.locator('#report-location').fill(uniqueLocation);
    await page.locator('[data-report-next]').first().click();
    await page.locator('#report-description').fill('Automated Playwright incident report.');
    await page.locator('[data-report-submit]').click();

    await expect(page.locator('[data-report-drawer]')).not.toHaveClass(/is-open/);
    await expect(page.locator('.toast-container')).toContainText(/submitted/i);
  });

  test('closes drawer with escape key', async ({ page }) => {
    await page.locator('[data-open-report-drawer]').first().click();
    await expect(page.locator('[data-report-drawer]')).toHaveClass(/is-open/);
    await page.keyboard.press('Escape');
    await expect(page.locator('[data-report-drawer]')).not.toHaveClass(/is-open/);
  });
});
