import { expect, Page } from '@playwright/test';
import { DemoUser } from './users';

export async function loginAs(page: Page, user: DemoUser): Promise<void> {
  await page.goto('/login.php');
  await page.locator('#email').fill(user.email);
  await page.locator('#password').fill(user.password);
  await page.locator('[data-login-submit]').first().click();
  await page.waitForURL(new RegExp(`${escapeRegex(user.landingPath)}(\\?.*)?$`));
}

export async function expectLoggedInShell(page: Page): Promise<void> {
  await expect(page.locator('.app-shell')).toBeVisible();
  await expect(page.locator('.app-sidebar, .app-mobile-header')).toBeVisible();
}

function escapeRegex(value: string): string {
  return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}
