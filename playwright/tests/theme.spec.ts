import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

test.describe('Theme toggle', () => {
  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 });
    await loginAs(page, USERS.community_reporter);
    await page.goto('/feed.php');
  });

  test('toggle switches data-theme on html element', async ({ page }) => {
    const toggle = page.locator('#darkModeToggle');
    await expect(toggle).toBeVisible();

    const initialTheme = await page.evaluate(() => document.documentElement.getAttribute('data-theme'));
    await toggle.click();
    const nextTheme = await page.evaluate(() => document.documentElement.getAttribute('data-theme'));
    expect(nextTheme).not.toBe(initialTheme);

    await toggle.click();
    const restoredTheme = await page.evaluate(() => document.documentElement.getAttribute('data-theme'));
    expect(restoredTheme).toBe(initialTheme);
  });

  test('theme preference persists after reload', async ({ page }) => {
    await page.locator('#darkModeToggle').click();
    const themeBeforeReload = await page.evaluate(() => document.documentElement.getAttribute('data-theme'));

    await page.reload();
    const themeAfterReload = await page.evaluate(() => document.documentElement.getAttribute('data-theme'));
    expect(themeAfterReload).toBe(themeBeforeReload);
  });
});
