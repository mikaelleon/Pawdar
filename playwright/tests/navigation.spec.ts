import { test, expect } from '@playwright/test';
import { loginAs } from '../helpers/login';
import { USERS } from '../helpers/users';

const NAV_CASES: Array<{
  user: keyof typeof USERS;
  href: string;
  label: RegExp;
}> = [
  { user: 'community_reporter', href: 'feed.php', label: /feed/i },
  { user: 'community_reporter', href: 'map.php', label: /map/i },
  { user: 'community_reporter', href: 'registry.php', label: /registry/i },
  { user: 'community_reporter', href: 'first-aid.php', label: /first aid/i },
  { user: 'community_reporter', href: 'breeds.php', label: /breeds/i },
  { user: 'lgu_official', href: 'cases.php', label: /cases/i },
  { user: 'admin', href: 'admin.php', label: /admin/i },
  { user: 'rescue_org', href: 'rescue.php', label: /rescue board/i },
];

test.describe('Sidebar navigation', () => {
  test('desktop sidebar links reach expected pages', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 });

    for (const navCase of NAV_CASES) {
      await loginAs(page, USERS[navCase.user]);
      const link = page.locator(`.sidebar-nav a[href="${navCase.href}"]`);
      await expect(link).toBeVisible();
      await link.click();
      await expect(page).toHaveURL(new RegExp(navCase.href.replace('.', '\\.')));
      await page.context().clearCookies();
    }
  });

  test('incident detail page loads for logged-in user', async ({ page }) => {
    await loginAs(page, USERS.community_reporter);
    await page.goto('/feed.php');
    await page.locator('[data-incident-list] a[href*="incident.php"]').first().click();
    await expect(page.locator('.incident-detail-layout')).toBeVisible();
    await expect(page.locator('.app-shell')).toBeVisible();
  });

  test('map page renders incident map container', async ({ page }) => {
    await loginAs(page, USERS.community_reporter);
    await page.goto('/map.php');
    await expect(page.locator('#pawdar-map')).toBeVisible();
  });

  test('first aid page lists guides', async ({ page }) => {
    await loginAs(page, USERS.community_reporter);
    await page.goto('/first-aid.php');
    await expect(page.locator('.first-aid-layout, .split-layout, .feed-title')).toBeVisible();
  });
});
