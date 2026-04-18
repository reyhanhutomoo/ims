const { test, expect } = require('@playwright/test');

test('test login admin', async ({ page }) => {
    await page.goto('http://127.0.0.1:8000/');
    await page.getByRole('link', { name: ' Masuk ke Sistem' }).click();
    await page.locator('#email').click();
    await page.locator('#email').fill('admin1@gmail.com')
    await page.locator('#password').click();
    await page.locator('#password').fill('admin123');
    await page.getByRole('button', { name: 'Login' }).click();
    await page.getByRole('link', { name: 'User Image Admin1' }).click();
    await page.getByRole('link', { name: 'Sign out' }).click();
});