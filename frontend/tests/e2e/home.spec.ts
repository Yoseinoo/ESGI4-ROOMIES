import { test, expect } from '@playwright/test'

test('homepage loads and shows title', async ({ page }) => {
    //Auth first then test
    await page.goto('http://localhost:5173/login');
    await page.fill('input[type="email"]', 'test@test.fr');
    await page.fill('input[type="password"]', 'test');
    await page.click('button[type="submit"]');
    await page.waitForURL('http://localhost:5173'); // wait after login

    // Check the title
    await expect(page).toHaveTitle("Vite App")

    // Check for h1
    await expect(page.locator('h1')).toContainText('Roomies')

    // Check if "Room" heading exists
    await expect(page.locator('h2')).toContainText('Rooms disponibles')
})
