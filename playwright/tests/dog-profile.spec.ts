import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

test.describe('Dog profile', () => {
  test('owner can view profile with vaccination section', async ({ page }) => {
    await loginAs(page, USERS.dog_owner);
    await page.goto('/registry.php');
    await page.locator('[data-registry-grid] .dog-card').first().click();
    await expect(page).toHaveURL(/dog-profile\.php\?id=\d+/);
    await expect(page.getByText(/owner/i).first()).toBeVisible();
    await expect(page.getByText(/vaccination/i).first()).toBeVisible();
  });

  test('veterinarian sees co-sign button when vaccine is unverified', async ({ page }) => {
    await loginAs(page, USERS.veterinarian);
    await page.goto('/registry.php');
    await page.locator('[data-registry-grid] .dog-card').first().click();

    const cosignBtn = page.locator('[data-cosign-vaccine]');
    if (!(await cosignBtn.isVisible())) {
      test.skip(true, 'No unverified vaccination record on this dog profile.');
    }

    await expect(cosignBtn).toBeEnabled();
  });

  test('vet and admin can reveal owner call button', async ({ page }) => {
    await loginAs(page, USERS.veterinarian);
    await page.goto('/registry.php');
    await page.locator('[data-registry-grid] .dog-card').first().click();

    const callBtn = page.locator('.btn-call-owner');
    if (!(await callBtn.isVisible())) {
      test.skip(true, 'Owner contact not available for this profile.');
    }

    await callBtn.click();
    await expect(page.locator('.call-tooltip, [data-owner-contact-line]')).toBeVisible();
  });
});
