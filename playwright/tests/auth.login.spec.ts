import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

test.describe('Authentication', () => {
  test('login page renders form and forgot password link', async ({ page }) => {
    await page.goto('/login.php');
    await expect(page.locator('h1.auth-title')).toContainText(/log in/i);
    await expect(page.locator('#email')).toBeVisible();
    await expect(page.locator('#password')).toBeVisible();
    await expect(page.locator('a.forgot-link')).toHaveAttribute('href', 'forgot_password.php');
  });

  test('invalid credentials show error state', async ({ page }) => {
    await page.goto('/login.php');
    await page.locator('#email').fill('not-a-user@example.com');
    await page.locator('#password').fill('wrong-password');
    await page.locator('[data-login-submit]').first().click();
    await page.waitForURL(/login\.php\?error=invalid/);
    await expect(page.locator('#login-form')).toHaveAttribute('data-login-error', '1');
  });

  test('community reporter lands on feed after login', async ({ page }) => {
    await loginAs(page, USERS.community_reporter);
    await expect(page).toHaveURL(/feed\.php/);
    await expect(page.locator('.feed-title')).toContainText(/nearby incidents/i);
  });

  test('dog owner lands on feed after login', async ({ page }) => {
    await loginAs(page, USERS.dog_owner);
    await expect(page).toHaveURL(/feed\.php/);
  });

  test('veterinarian lands on registry after login', async ({ page }) => {
    await loginAs(page, USERS.veterinarian);
    await expect(page).toHaveURL(/registry\.php/);
  });

  test('lgu official lands on cases after login', async ({ page }) => {
    await loginAs(page, USERS.lgu_official);
    await expect(page).toHaveURL(/cases\.php/);
  });

  test('rescue org lands on rescue board after login', async ({ page }) => {
    await loginAs(page, USERS.rescue_org);
    await expect(page).toHaveURL(/rescue\.php/);
  });

  test('admin lands on admin console after login', async ({ page }) => {
    await loginAs(page, USERS.admin);
    await expect(page).toHaveURL(/admin\.php/);
    await expect(page.locator('.feed-title')).toContainText(/admin console/i);
  });
});
