import { test, expect } from '@playwright/test';

test.describe('Public and auth UI shell', () => {
  test('landing page loads marketing content', async ({ page }) => {
    await page.goto('/index.php');
    await expect(page.locator('body')).toContainText(/pawdar/i);
    await expect(page.locator('a[href="login.php"], a[href="signup.php"]').first()).toBeVisible();
  });

  test('signup page renders role cards', async ({ page }) => {
    await page.goto('/signup.php');
    await expect(page.locator('.auth-page')).toBeVisible();
    await expect(page.locator('input[type="radio"][name="role"]')).toHaveCount(6);
  });

  test('login page uses auth layout without app shell', async ({ page }) => {
    await page.goto('/login.php');
    await expect(page.locator('.auth-page')).toBeVisible();
    await expect(page.locator('.app-shell')).toHaveCount(0);
  });

  test('protected route redirects anonymous users to login', async ({ page }) => {
    await page.goto('/feed.php');
    await expect(page).toHaveURL(/login\.php/);
  });
});

test.describe('Responsive layout', () => {
  test('mobile viewport hides desktop sidebar', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/login.php');
    await page.locator('#email').fill('maria.santos@email.com');
    await page.locator('#password').fill('password');
    await page.locator('[data-login-submit]').first().click();
    await page.waitForURL(/feed\.php/);

    await expect(page.locator('.app-sidebar.hidden-mobile')).toBeHidden();
    await expect(page.locator('.app-mobile-header.hidden-desktop')).toBeVisible();
  });
});
