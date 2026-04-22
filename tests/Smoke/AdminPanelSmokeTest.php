<?php

/**
 * HTTP smoke tests for the Filament admin panel.
 *
 * Authenticates as an EXISTING super_admin and ensures every resource index
 * page returns HTTP 200. Does not create records — safe against a shared DB.
 */
beforeEach(function () {
    $this->actingAs(existingSuperAdmin(), 'web');
});

it('admin resource index pages respond with 200', function (string $slug) {
    $this->get('/'.$slug)->assertOk();
})->with(adminResourceSlugs());

it('admin dashboard responds with 200', function () {
    $this->get('/admin')->assertOk();
});
