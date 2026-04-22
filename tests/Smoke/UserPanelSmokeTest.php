<?php

/**
 * HTTP smoke tests for the Filament user panel.
 *
 * Uses an EXISTING verified user that is linked to legacy data via
 * `prev_user_id`, since every user-panel page expects it. Tests are skipped
 * if no such user exists locally.
 *
 * A response is considered "smoke-OK" if the app returns any client-level
 * status (200 / 302 / 403). A 5xx is always a failure.
 */
beforeEach(function () {
    $this->actingAs(existingPanelUser(requirePrevUserId: true), 'web');
});

it('user panel pages respond without server errors', function (string $slug) {
    $status = $this->get('/'.$slug)->status();

    expect($status)->toBeLessThan(500, "GET /{$slug} returned server error {$status}");
})->with(userPanelSlugs());
