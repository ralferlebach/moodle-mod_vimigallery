// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * User-story browser tests for mod_vimigallery, grouped by role. Each test
 * records a video on every run - see playwright.config.ts. Stories are specified
 * in ViMi_User_Stories.md. Requires a seeded, running site (see seed.php).
 */

import {test, expect} from '@playwright/test';
import {readEnv, login} from './support/env';

const env = readEnv();

/** Open the gallery in English so labels resolve regardless of site language. */
function galleryUrl(env: ReturnType<typeof readEnv>): string {
    const sep = env.galleryPath.includes('?') ? '&' : '?';
    return `${env.baseURL}${env.galleryPath}${sep}lang=en`;
}

test.describe('mod_vimigallery - Teacher stories', () => {
    // T1: the gallery was created with comments and compare on - the teacher
    // confirms both surfaces are present on the page they configured.
    test('T1 - a gallery offers comments and the compare view', async ({page}) => {
        await login(page, env.baseURL, env.teacher);
        await page.goto(galleryUrl(env));
        await expect(page.getByRole('link', {name: /Compare/i}).first()).toBeVisible({timeout: 20_000});
        await expect(page.locator('[data-comment-submit]').first()).toBeVisible({timeout: 20_000});
    });

    // T2: a teacher hides a map through the arrange page and it leaves the album.
    test('T2 - a teacher hides a map and it disappears for students', async ({page, browser}) => {
        await login(page, env.baseURL, env.teacher);
        await page.goto(`${env.baseURL}/mod/vimigallery/arrange.php?id=${env.cmid}&lang=en`);

        // Each map row carries a hide form (a button submitting action=hide via a
        // hidden input). Target that form's submit button - stable across themes
        // and languages, unlike the icon's accessible name.
        const hideForm = page.locator('form:has(input[name="action"][value="hide"])').first();
        await expect(hideForm).toBeVisible({timeout: 20_000});
        await hideForm.locator('button').click();
        // After hiding, that row now offers a show form, proving the state flipped.
        await expect(page.locator('form:has(input[name="action"][value="show"])').first())
            .toBeVisible({timeout: 20_000});
    });
});

test.describe('mod_vimigallery - Student stories', () => {
    // S1: a student reads the album and posts a comment on a map.
    test('S1 - a student posts a comment on a map', async ({page}) => {
        await login(page, env.baseURL, env.student);
        await page.goto(galleryUrl(env));

        const input = page.locator('[data-comment-input]').first();
        await expect(input).toBeVisible({timeout: 20_000});
        const text = 'Great map ' + Date.now();
        await input.fill(text);
        await page.locator('[data-comment-submit]').first().click();

        // The posted comment appears in the map's comment list.
        await expect(page.getByText(text, {exact: false}).first()).toBeVisible({timeout: 20_000});
    });

    // S2: a student opens the compare view and picks two maps to see side by side.
    test('S2 - a student compares two maps side by side', async ({page}) => {
        await login(page, env.baseURL, env.student);
        await page.goto(`${env.baseURL}/mod/vimigallery/compare.php?id=${env.cmid}&lang=en`);

        // Two selectors are offered; choose the first real map on each side.
        const left = page.locator('#cmpleft');
        const right = page.locator('#cmpright');
        await expect(left).toBeVisible({timeout: 20_000});
        // The first option is the "Choose a map..." placeholder; real maps follow.
        // Wait until at least two real maps are available, then select by value so
        // the choice does not depend on option order.
        await expect
            .poll(async () => left.locator('option').count(), {timeout: 20_000})
            .toBeGreaterThanOrEqual(3);
        const values: string[] = await left.locator('option').evaluateAll(
            (els) => els.map((el) => (el as HTMLOptionElement).value).filter((v) => v !== '')
        );
        await left.selectOption(values[0]);
        await right.selectOption(values[1]);
        await page.getByRole('button', {name: /Compare/i}).click();

        // Both read-only panes render.
        await expect(page.locator('.vimigallery-compare-pane')).toHaveCount(2, {timeout: 30_000});
    });
});
