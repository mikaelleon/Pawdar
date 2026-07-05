import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

test.describe('Dog registry', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page, USERS.dog_owner);
    await page.goto('/registry.php');
  });

  test('registry page shows summary strip and dog grid', async ({ page }) => {
    await expect(page.locator('.page-title-row .feed-title, h1')).toContainText(/dog registry/i);
    await expect(page.locator('.summary-strip')).toBeVisible();
    await expect(page.locator('[data-registry-grid] .dog-card').first()).toBeVisible();
  });

  test('view switcher toggles layout modes', async ({ page }) => {
    const listBtn = page.locator('[data-registry-view-btn][data-registry-view="list"]');
    await listBtn.click();
    await expect(page.locator('[data-registry-grid]')).toHaveAttribute('data-registry-view', 'list');

    const tilesBtn = page.locator('[data-registry-view-btn][data-registry-view="tiles"]');
    await tilesBtn.click();
    await expect(page.locator('[data-registry-grid]')).toHaveAttribute('data-registry-view', 'tiles');
  });

  test('dog card navigates to profile', async ({ page }) => {
    await page.locator('[data-registry-grid] .dog-card a, [data-registry-grid] .dog-card').first().click();
    await expect(page).toHaveURL(/dog-profile\.php\?id=\d+/);
    await expect(page.locator('.profile-name, h1')).toBeVisible();
  });

  test('register dog link is available for dog owner', async ({ page }) => {
    await expect(page.locator('a[href="register_dog.php"]')).toBeVisible();
  });
});
