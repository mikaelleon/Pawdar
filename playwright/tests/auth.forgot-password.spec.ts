import { test, expect } from '@playwright/test';

test.describe('Forgot password flow', () => {
  test('forgot password page loads and accepts email', async ({ page }) => {
    await page.goto('/forgot_password.php');
    await expect(page.locator('h1.auth-title')).toContainText(/forgot password/i);
    await expect(page.locator('a[href="login.php"]')).toBeVisible();
    await page.locator('#email').fill('maria.santos@email.com');
    await page.locator('button[type="submit"]').click();
    await expect(page.getByText(/if an account exists with that email/i)).toBeVisible();
  });

  test('reset password rejects invalid token', async ({ page }) => {
    await page.goto('/reset_password.php?token=invalid-token-value');
    await expect(page.getByText(/invalid or has expired/i)).toBeVisible();
    await expect(page.locator('a[href="forgot_password.php"]')).toBeVisible();
  });
});
