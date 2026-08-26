export default async function run(page) {
  const responses = [];
  page.on('response', async (response) => {
    if (response.url().includes('/dashboard')) {
      responses.push({ status: response.status(), body: (await response.text()).slice(0, 1200) });
    }
  });
  await page.getByLabel('Email').fill('admin@pios.test');
  await page.getByLabel('Mot de passe').fill('password');
  await page.getByRole('button', { name: 'Se connecter' }).click({ noWaitAfter: true });
  await page.waitForTimeout(5000);
  return { url: page.url(), body: (await page.locator('body').innerText()).slice(0, 800), responses };
}
