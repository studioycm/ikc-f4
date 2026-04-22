<?php

use App\Models\User;
use Filament\Facades\Filament;
use Pest\Browser\Api\AwaitableWebpage;
use Pest\Browser\Api\Webpage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case bindings
|--------------------------------------------------------------------------
|
| Tests NEVER mutate the production database. They pick existing users from
| the already-seeded local DB and rely on read-only queries or in-memory
| sqlite schemas (see UserPrevUserIdUniquenessTest).
|
*/

pest()->extend(TestCase::class)->in('Feature', 'Unit', 'Smoke', 'Browser');

/*
|--------------------------------------------------------------------------
| Browser Testing (Pest 4 plugin)
|--------------------------------------------------------------------------
|
| Pest's default action/navigation timeout is 5 seconds, which is too tight
| for Filament's first-paint (icons, translations, Vite bundle, Livewire
| hydration). 30 seconds gives a comfortable budget without hiding real
| regressions.
|
| Ref: https://pestphp.com/docs/browser-testing#configuring-timeouts
|
*/

pest()->browser()->timeout(10000);
/*
|--------------------------------------------------------------------------
| Browser login helpers
|--------------------------------------------------------------------------
|
| The Pest browser runs Playwright in a real Chromium instance; it does NOT
| share the PHP test process's session cookie, so `$this->actingAs()` has
| no effect on `visit()`. Tests must authenticate through the real Filament
| login form. Credentials are for existing, locally-seeded users — no DB
| writes, no factories. Override with env vars in CI.
|
| Ref: https://pestphp.com/docs/browser-testing
|
*/
const BROWSER_SUPER_ADMIN_EMAIL = 'ycm@data4.work';
const BROWSER_SUPER_ADMIN_PASSWORD = 'y0n1@1kc';
const BROWSER_PANEL_USER_EMAIL = 'photoycm@gmail.com';
const BROWSER_PANEL_USER_PASSWORD = 'y0n1@1kc';

function browserLogin(string $panel, string $email, string $password): AwaitableWebpage
{
    $page = visit("/{$panel}/login")
        ->type('#form [type="email"]', $email)
        ->type('#form [type="password"]', $password)
//        ->click('#form button[type="submit"]')
//        ->submit()
        ->press(__('filament-panels::pages/auth/login.form.actions.authenticate.label'));
    $page->wait(5);
    return $page;
}

function browserLoginAsSuperAdmin(): AwaitableWebpage
{
    return browserLogin('admin', BROWSER_SUPER_ADMIN_EMAIL, BROWSER_SUPER_ADMIN_PASSWORD);
}

function browserLoginAsPanelUser(): AwaitableWebpage
{
    return browserLogin('user', BROWSER_PANEL_USER_EMAIL, BROWSER_PANEL_USER_PASSWORD);
}

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Helpers — existing users (NO factory creation, NO DB writes)
|--------------------------------------------------------------------------
|
| These helpers resolve users that already exist in the local database and
| hold the required role. Tests must never insert new rows; if a suitable
| user is missing the test will be skipped instead of polluting the DB.
|
*/

function existingUserWithRole(string $roleName, bool $requirePrevUserId = false): ?User
{
    $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

    if ($role === null) {
        return null;
    }

    $query = User::whereHas('roles', fn ($q) => $q->where('roles.id', $role->id))
        ->whereNotNull('email_verified_at');

    if ($requirePrevUserId) {
        $query->whereNotNull('prev_user_id');
    }

    return $query->orderBy('id')->first();
}

function existingSuperAdmin(): User
{
    $user = existingUserWithRole('super_admin');

    if ($user === null) {
        test()->markTestSkipped('No verified super_admin user found in the database.');
    }

    return $user;
}

function existingPanelUser(bool $requirePrevUserId = false): User
{
    $user = existingUserWithRole('panel_user', $requirePrevUserId)
        ?? existingUserWithRole('admin', $requirePrevUserId)
        ?? existingUserWithRole('super_admin', $requirePrevUserId);

    if ($user === null) {
        test()->markTestSkipped(
            $requirePrevUserId
                ? 'No verified panel user with a linked prev_user_id was found in the database.'
                : 'No verified panel user was found in the database.'
        );
    }

    return $user;
}

/**
 * Authenticate as an existing user inside a Filament panel.
 * Does not create any DB records.
 */
function loginToFilamentAs(User $user, string $panelId = 'admin'): User
{
    $panel = Filament::getPanel($panelId);
    Filament::setCurrentPanel($panel);

    test()->actingAs($user, 'web');
    Filament::auth()->login($user);

    return $user;
}

/**
 * @return array<int, string> Absolute slugs for every Filament admin resource index page.
 */
function adminResourceSlugs(): array
{
    return [
        'admin',
        'admin/breeding-inquiries',
        'admin/breeding-related-dogs',
        'admin/prev-breeding-houses',
        'admin/prev-breedings',
        'admin/prev-breeds',
        'admin/prev-clubs',
        'admin/prev-colors',
        'admin/prev-dog-documents',
        'admin/prev-dog-imports',
        'admin/prev-dogs',
        'admin/prev-hairs',
        'admin/prev-healths',
        'admin/prev-judges',
        'admin/prev-payments',
        'admin/prev-prices',
        'admin/prev-show-arenas',
        'admin/prev-show-breeds',
        'admin/prev-show-classes',
        'admin/prev-show-dogs',
        'admin/prev-show-payments',
        'admin/prev-show-registrations',
        'admin/prev-show-results',
        'admin/prev-shows',
        'admin/prev-skill-users',
        'admin/prev-titles',
        'admin/prev-user-activities',
        'admin/prev-user-requests',
        'admin/prev-user-tasks',
        'admin/prev-users',
        'admin/prev-vet-auths',
        'admin/users',
    ];
}

/**
 * @return array<int, string> User-panel page slugs.
 */
function userPanelSlugs(): array
{
    return [
        'user',
        'user/breeding-activity',
        'user/breeding-inquiries',
        'user/dogs',
        'user/memberships',
        'user/payments',
        'user/requests',
        'user/shows',
    ];
}
