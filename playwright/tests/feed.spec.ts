import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

test.describe('Feed page', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page, USERS.community_reporter);
  });

  test('shows incident feed and filter chips', async ({ page }) => {
    await expect(page.locator('[data-feed-page]')).toBeVisible();
    await expect(page.locator('.feed-title')).toContainText(/nearby incidents/i);
    await expect(page.locator('[data-filter-chips] .filter-chip')).toHaveCount(6);
  });

  test('filter chips update URL and reload feed', async ({ page }) => {
    const biteChip = page.locator('[data-filter-chips] .filter-chip[data-filter="bite"]');
    await biteChip.click();
    await page.waitForURL(/filter=bite/);
    await expect(biteChip).toHaveClass(/chip-active/);
  });

  test('incident cards link to detail page', async ({ page }) => {
    const firstCardLink = page.locator('[data-incident-list] a[href*="incident.php"]').first();
    await expect(firstCardLink).toBeVisible();
    await firstCardLink.click();
    await expect(page).toHaveURL(/incident\.php\?id=\d+/);
    await expect(page.locator('.feed-title')).toBeVisible();
  });

  test('notification bell toggles dropdown without navigating away', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 });
    const bell = page.locator('[data-notification-bell]').first();
    await bell.click();
    await expect(page.locator('[data-notification-dropdown]')).toBeVisible();
    await bell.click();
    await expect(page.locator('[data-notification-dropdown]')).toBeHidden();
    await expect(page).toHaveURL(/feed\.php/);
  });
});
