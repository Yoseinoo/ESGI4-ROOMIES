import { test, expect } from '@playwright/test'

test('user can send a message', async ({ page }) => {
    //Auth first then test
    await page.goto('http://localhost:5173/login');
    await page.fill('input[type="email"]', 'test@test.fr');
    await page.fill('input[type="password"]', 'test');
    await page.click('button[type="submit"]');
    await page.waitForURL('http://localhost:5173'); // wait after login

    const input = page.locator('input[placeholder="Tape ton message"]')
    await input.fill('Hello from test')
    await input.press('Enter')

    // Wait for the message to appear
    await expect(page.locator('.message')).toContainText('Hello from test')
})