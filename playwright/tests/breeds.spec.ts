import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

test.describe('Breed directory', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page, USERS.community_reporter);
    await page.goto('/breeds.php');
  });

  test('loads breed grid and size filter chips', async ({ page }) => {
    await expect(page.locator('[data-breeds-page]')).toBeVisible();
    await expect(page.locator('[data-breed-grid] .breed-card').first()).toBeVisible();
    await expect(page.locator('[data-size-chips] .breed-size-chip')).toHaveCount(4);
  });

  test('search filters breed cards', async ({ page }) => {
    await page.locator('#breed-search').fill('Labrador');
    await page.waitForTimeout(400);
    await expect(page.locator('[data-breed-grid] .breed-card')).not.toHaveCount(0);
    await expect(page.locator('[data-breed-grid]')).toContainText(/labrador/i);
  });

  test('size chip filters breeds', async ({ page }) => {
    await page.locator('[data-size-chips] .breed-size-chip[data-size="Small"]').click();
    await page.waitForURL(/size=Small/);
    await expect(page.locator('[data-breeds-page]')).toHaveAttribute('data-size-filter', 'Small');
  });

  test('selecting a breed card highlights it', async ({ page }) => {
    const card = page.locator('[data-breed-grid] .breed-card').first();
    await card.click();
    await expect(card).toHaveClass(/is-selected/);
  });
});
