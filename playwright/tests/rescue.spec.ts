import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

test.describe('Rescue board', () => {
  test.beforeEach(async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 });
    await loginAs(page, USERS.rescue_org);
  });

  test('rescue board renders panels', async ({ page }) => {
    await expect(page.locator('.feed-title')).toContainText(/rescue board/i);
    await expect(page.getByText(/adoption listings/i)).toBeVisible();
  });

  test('status dropdown updates badge when tracked case exists', async ({ page }) => {
    const statusSelect = page.locator('[data-rescue-status]').first();
    if (!(await statusSelect.isVisible())) {
      test.skip(true, 'No tracked rescue cases seeded for this org.');
    }

    const caseId = await statusSelect.getAttribute('data-rescue-status');
    const badge = page.locator(`[data-rescue-badge="${caseId}"]`);
    const originalStatus = (await badge.textContent())?.trim() ?? '';

    const options = ['Spotted', 'Rescued', 'Under Vet Care', 'Ready for Adoption'];
    const nextStatus = options.find((status) => status !== originalStatus) ?? 'Rescued';

    await statusSelect.selectOption(nextStatus);
    await expect(badge).toHaveText(nextStatus);
  });
});
