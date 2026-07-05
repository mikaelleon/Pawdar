import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

test.describe('Register dog form', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page, USERS.dog_owner);
    await page.goto('/register_dog.php');
  });

  test('step 1 continue stays disabled until name and breed are set', async ({ page }) => {
    const continueBtn = page.locator('[data-step-next]');
    await expect(continueBtn).toBeDisabled();

    await page.locator('#dog-name').fill('Playwright Pup');
    await expect(continueBtn).toBeDisabled();

    await page.locator('[data-breed-input]').fill('Lab');
    await page.waitForTimeout(400);
    const dropdownItem = page.locator('.breed-dropdown-item').first();
    await expect(dropdownItem).toBeVisible({ timeout: 15_000 });
    await dropdownItem.click();

    await expect(continueBtn).toBeEnabled();
  });

  test('step 2 blocks partial vaccine records without dates', async ({ page }) => {
    await page.locator('#dog-name').fill('Playwright Pup');
    await page.locator('[data-breed-input]').fill('Aspin');
    await page.waitForTimeout(400);
    await page.locator('.breed-use-custom').click({ timeout: 10_000 }).catch(async () => {
      const item = page.locator('.breed-dropdown-item').first();
      if (await item.isVisible()) {
        await item.click();
      }
    });

    await page.locator('[data-step-next]').click();
    await expect(page.locator('[data-form-step="2"]')).toBeVisible();

    await page.locator('#vaccine-name').fill('Anti-Rabies');
    await page.locator('[data-step-next]').click();
    await expect(page.locator('[data-form-step="2"]')).toBeVisible();
    await expect(page.locator('[data-field-error], .field-error').first()).toBeVisible();
  });

  test('step 2 can be skipped when health fields are empty', async ({ page }) => {
    await page.locator('#dog-name').fill('Skip Health Pup');
    await page.locator('[data-breed-input]').fill('Aspin');
    await page.waitForTimeout(400);
    await page.locator('.breed-use-custom').click({ timeout: 10_000 }).catch(async () => {
      const item = page.locator('.breed-dropdown-item').first();
      if (await item.isVisible()) {
        await item.click();
      }
    });

    await page.locator('[data-step-next]').click();
    await page.locator('[data-step-next]').click();
    await expect(page.locator('[data-form-step="3"]')).toBeVisible();
    await expect(page.locator('#register-review')).toContainText('Skip Health Pup');
  });

  test('stepper shows progress across steps', async ({ page }) => {
    await expect(page.locator('[data-register-step-indicator="1"]')).toHaveClass(/is-active/);
    await page.locator('#dog-name').fill('Stepper Test');
    await page.locator('[data-breed-input]').fill('Aspin');
    await page.waitForTimeout(400);
    await page.locator('.breed-use-custom').click({ timeout: 10_000 }).catch(async () => {
      const item = page.locator('.breed-dropdown-item').first();
      if (await item.isVisible()) {
        await item.click();
      }
    });
    await page.locator('[data-step-next]').click();
    await expect(page.locator('[data-register-step-indicator="1"]')).toHaveClass(/is-done/);
    await expect(page.locator('[data-register-step-indicator="2"]')).toHaveClass(/is-active/);
  });
});
