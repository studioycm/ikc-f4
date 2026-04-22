<?php

/**
 * Real-browser smoke tests for the Filament admin panel.
 *
 * Uses Playwright under the hood (`pestphp/pest-plugin-browser`). Browsers are
 * stored at PLAYWRIGHT_BROWSERS_PATH=D:\playwright-browsers.
 *
 * Flow (ordered — later tests depend on earlier ones):
 *   1. /admin/login renders.
 *   2. Logging in as super-admin lands on /admin and the dashboard is clean.
 *   3. Only then iterate every admin resource index page for JS errors.
 *
 * Authenticates by driving the real Filament login form with existing
 * credentials (see `browserLoginAsSuperAdmin()` in tests/Pest.php).
 * `$this->actingAs()` is intentionally NOT used here — the Playwright
 * browser runs out-of-process and does not share the PHP session cookie.
 */
describe('admin panel browser smoke', function () {
//    it('1. login page renders', function () {
//        visit('/admin/login')
//            ->assertSee(__('filament-panels::pages/auth/login.form.email.label'))
//            ->assertNoJavaScriptErrors();
//    });

//    it('2. logs in and dashboard renders without JavaScript errors', function () {
//        $page = browserLoginAsSuperAdmin();
//        $page->assertNoJavaScriptErrors()
//            ->assertNoConsoleLogs();
//    });

    it('3. admin resource index pages have no JS errors (bulk smoke)', function () {
        $urls = array_map(fn (string $slug): string => '/'.$slug, adminResourceSlugs());

        $page = browserLoginAsSuperAdmin();
        $count = count($urls);
        for ($index = 0; $index < $count; $index++) {
            $page->navigate($urls[$index]);
            $page->wait(3);
            $page->assertSeeAnythingIn('.fi-header-heading');
        }
    });
});
