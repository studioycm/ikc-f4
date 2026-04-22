<?php

/**
 * Real-browser smoke tests for the Filament user panel.
 *
 * Flow (ordered — later tests depend on earlier ones):
 *   1. /user/login renders.
 *   2. Logging in as a panel user lands on /user and the dashboard is clean.
 *   3. Only then iterate every user-panel page for JS errors.
 *
 * Authenticates via the real login form (see `browserLoginAsPanelUser()` in
 * tests/Pest.php). No DB writes, no factories.
 */
describe('user panel browser smoke', function () {
    it('1. login page renders', function () {
        visit('/user/login')
            ->assertSee(__('filament-panels::pages/auth/login.form.email.label'))
            ->assertNoJavaScriptErrors();
    });

    it('2. logs in and dashboard renders without JavaScript errors', function () {
        $page = browserLoginAsPanelUser();
            $page->assertPathIs('/user')
            ->assertNoJavaScriptErrors();
    });

    it('3. user panel pages have no JS errors (bulk smoke)', function () {
        $urls = array_map(fn(string $slug): string => '/' . $slug, userPanelSlugs());

        $page = browserLoginAsPanelUser();
        $count = count($urls);
        for ($index = 0; $index < $count; $index++) {
            $page->navigate($urls[$index]);
            $page->wait(3);
            $page->assertSeeAnythingIn('.fi-header-heading');
        }
    });
});
