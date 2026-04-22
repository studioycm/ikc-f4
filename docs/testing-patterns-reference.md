# Testing Patterns Reference (Pest 4 + Filament 4 + Livewire 3)

This file collects reusable assertion patterns distilled from the one-time AI-generated
Filament 3 → Filament 4 / PHPUnit 11 → 12 upgrade tests that were moved to
`tests/_archive/` (git-ignored). Copy from here when you write **new** real tests.

---

## 1. Authenticate against a Filament panel without factory writes

```php
// tests/Pest.php already exposes these helpers.
$user = existingSuperAdmin();            // reads an existing verified super_admin
$user = existingPanelUser();             // reads an existing verified panel_user/admin
loginToFilamentAs($user, 'admin');       // panel-scoped auth (does NOT write to DB)
```

- `actingAs($user, 'web')` is enough for plain HTTP tests (`$this->get(...)`).
- Livewire component tests additionally require `loginToFilamentAs()` so
  `Filament::getCurrentPanel()` is resolved.

---

## 2. Assert Filament table column labels / types

```php
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use function Pest\Livewire\livewire;

livewire(ListUsers::class)
    ->assertTableColumnExists('roles.name', fn (TextColumn $c): bool => $c->getLabel() === __('Role'))
    ->assertTableColumnExists('email_verified_at', fn (IconColumn $c): bool => $c->getLabel() === __('Verified'));
```

## 3. Assert Filament table action / bulk-action labels

```php
livewire(ListUsers::class)
    ->assertTableActionHasLabel('send_email', __('Send Email'), $record)
    ->assertTableBulkActionHasLabel('bulk_send_email', __('Send Email'));
```

## 4. Inspect a Filament resource’s table filters directly

Useful for checking that a filter is the right subclass or that search /
option-limit tweaks from the Filament 4 migration are still in effect.

```php
$component = livewire(ListPrevDogs::class);
$filters = $component->instance()->getTable()->getFilters();

expect($filters['trashed'])->toBeInstanceOf(TrashedFilter::class);
expect($filters['owners'])->toBeInstanceOf(SelectFilter::class);
expect($filters['owners']->getFormField()->isSearchable())->toBeTrue();
expect($filters['owners']->getFormField()->getOptionsLimit())->toBe(50);
```

## 5. Assert a Filament resource’s Eloquent query is still valid

Catches `Cannot use ::class on null` regressions after model / resource refactors.

```php
$builder = PrevShowResultResource::getEloquentQuery();

expect($builder)->toBeInstanceOf(Builder::class);
expect($builder->getModel())->toBeInstanceOf(Model::class);
expect(get_class($builder->getModel()))->toBe(PrevShowResultResource::getModel());
```

## 6. Livewire component tests without hitting the DB

```php
Livewire::test(ClubMembershipCompact::class, ['membershipState' => [...]])
    ->assertSee('Terrier Club')
    ->assertSee(__('More details'));

Livewire::test(PedigreeTree::class, ['dogId' => 123])
    ->assertSet('depth', 4)
    ->set('settingsData.depth', 7)
    ->call('submitSettings')
    ->assertSet('depth', 7);
```

Bind mocked services in `beforeEach` via `$this->app->bind(...)` instead of
creating fixture records:

```php
$this->app->bind(PedigreeTreeBuilderService::class, fn () => new class extends PedigreeTreeBuilderService {
    public function __construct() {}
    public function build(int $dogId, int $depth = 4, string $direction = 'rtl', bool $includeNodeTitles = false): array {
        return [/* fake tree */];
    }
});
```

## 7. Hebrew / locale assertions

```php
app()->setLocale('he');

expect(__('Subject'))->toBe('נושא')
    ->and(__('Arena'))->toBe('זירה')
    ->and(__('common.labels.metadata'))->toBe('נתוני מערכת');
```

## 8. Model-relationship type sanity (no DB)

```php
expect((new PrevShowDog)->show())->toBeInstanceOf(BelongsTo::class);
expect((new PrevShow)->classes())->toBeInstanceOf(HasMany::class);

// Key inspection:
$rel = (new User)->prevUser();
expect($rel->getForeignKeyName())->toBe('prev_user_id');
expect($rel->getOwnerKeyName())->toBe('id');
```

## 9. Accessor / computed-attribute tests with in-memory relations

```php
$dog = new PrevDog(['BirthDate' => Carbon::parse('2020-01-10'), 'GenderID' => LegacyDogGender::Female]);
$dog->setRelation('femaleBreedings', new Collection([new PrevBreeding(['birthing_date' => '2023-05-01'])]));

expect($dog->age_years)->toBe('4y 5m (53m)');
```

## 10. Legacy MySQL read-only conformance (archived pattern)

Only useful as a one-off data-quality sweep against `mysql_prev`; not suitable
for the standard test suite. If revived, keep it in a dedicated
`tests/LegacyData/` directory outside the default testsuites (requires dev DB).

## 11. Pest 4 Browser testing

```php
use function Pest\Browser\visit;

it('logs in to the admin panel', function () {
    $admin = existingSuperAdmin();
    $this->actingAs($admin, 'web');

    visit('/admin')
        ->assertSee($admin->name)
        ->assertNoJavaScriptErrors();
});

// Smoke across many URLs in one shot:
visit(adminResourceSlugs())->assertNoSmoke();
```

Browser binaries live under `PLAYWRIGHT_BROWSERS_PATH=D:\playwright-browsers`
on this workstation. Tests land under `tests/Browser/` and screenshots are
git-ignored (`tests/Browser/Screenshots`).
