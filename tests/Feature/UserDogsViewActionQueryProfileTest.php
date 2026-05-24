<?php

use App\Filament\User\Widgets\Sections\UserDogsTable;
use App\Models\PrevDog;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use function Pest\Livewire\livewire;

function userDogsViewActionDiagnosticSamples(): array
{
    return [
        'timeout_candidate_sagir_1022274' => [
            'dog_id' => 252577,
            'sagir_id' => 1022274,
        ],
        'timeout_candidate_sagir_1003687' => [
            'dog_id' => 233936,
            'sagir_id' => 1003687,
        ],
        'slow_but_loads_sagir_1027315' => [
            'dog_id' => 257618,
            'sagir_id' => 1027315,
        ],
    ];
}

function userDogsViewActionDiagnosticUser(): User
{
    $user = User::query()
        ->where('prev_user_id', 258298)
        ->first();

    if (! $user instanceof User) {
        test()->markTestSkipped('No application user linked to PrevUser id 258298 was found.');
    }

    return $user;
}

function userDogsViewActionDiagnosticArtifactPath(): string
{
    return storage_path('app/testing/user-dogs-view-action-query-profile.json');
}

function userDogsViewActionDiagnosticWriteArtifact(array $payload): void
{
    $path = userDogsViewActionDiagnosticArtifactPath();

    File::ensureDirectoryExists(dirname($path));
    File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function userDogsViewActionDiagnosticClassifyQuery(string $sql): string
{
    return match (true) {
        str_contains($sql, 'from `dogs_titles_db` where `TitleCode`') => 'titles.lazy_title_lookup',
        str_contains($sql, 'from `dogs_titles_db` inner join `Dogs_ScoresDB`') => 'titles.eager_titles',
        str_contains($sql, 'from `Dogs_ScoresDB`') => 'titles.scores',
        str_contains($sql, 'from `BreedsDB`') => 'breed',
        str_contains($sql, 'from `ColorsDB`') => 'color',
        str_contains($sql, 'from `HairsDB`') => 'hair',
        str_contains($sql, 'from `breedinghouses`') => 'breedinghouse',
        str_contains($sql, 'from `users` inner join `dogs2users`') && str_contains($sql, "`status` != 'current'") => 'owners.previous',
        str_contains($sql, 'from `users` inner join `dogs2users`') => 'owners.current',
        str_contains($sql, 'from `dogs2users`') => 'ownership_pivot',
        str_contains($sql, 'from `DogsDB`') && str_contains($sql, '`SagirID` in') => 'dog.parents_or_record_by_sagir',
        str_contains($sql, 'from `DogsDB`') => 'dog.record',
        default => 'other',
    };
}

function userDogsViewActionDiagnosticSummariseQueries(array $queries): array
{
    $groups = [];

    foreach ($queries as $query) {
        $group = userDogsViewActionDiagnosticClassifyQuery($query['sql']);

        $groups[$group] ??= [
            'query_count' => 0,
            'total_time_ms' => 0.0,
            'examples' => [],
        ];

        $groups[$group]['query_count']++;
        $groups[$group]['total_time_ms'] += $query['time_ms'];

        if (count($groups[$group]['examples']) < 5) {
            $groups[$group]['examples'][] = $query['sql'];
        }
    }

    foreach ($groups as &$group) {
        $group['total_time_ms'] = round($group['total_time_ms'], 2);
    }

    uasort(
        $groups,
        static fn (array $first, array $second): int => $second['total_time_ms'] <=> $first['total_time_ms'],
    );

    $slowestQueries = collect($queries)
        ->sortByDesc('time_ms')
        ->take(15)
        ->values()
        ->all();

    return [
        'query_count' => count($queries),
        'total_query_time_ms' => round((float) collect($queries)->sum('time_ms'), 2),
        'groups' => $groups,
        'slowest_queries' => $slowestQueries,
    ];
}

function userDogsViewActionDiagnosticIndexState(): array
{
    $tables = [
        'DogsDB',
        'dogs2users',
        'Dogs_ScoresDB',
        'dogs_titles_db',
        'breedinghouses',
        'BreedsDB',
        'ColorsDB',
        'HairsDB',
    ];

    $placeholders = implode(', ', array_fill(0, count($tables), '?'));

    return collect(DB::connection('mysql_prev')->select(
        "select table_name, index_name, group_concat(column_name order by seq_in_index separator ',') as columns
        from information_schema.statistics
        where table_schema = database()
            and table_name in ({$placeholders})
        group by table_name, index_name
        order by table_name, index_name",
        $tables,
    ))
        ->map(fn (object $index): array => [
            'table' => $index->table_name,
            'index' => $index->index_name,
            'columns' => $index->columns,
        ])
        ->all();
}

test('user dogs view action modal query profile identifies slow relationships', function () {
    set_time_limit(240);

    loginToFilamentAs(userDogsViewActionDiagnosticUser(), 'user');

    $profiles = [];
    $queries = [];
    $captureQueries = false;

    DB::listen(function (QueryExecuted $query) use (&$captureQueries, &$queries): void {
        if (! $captureQueries) {
            return;
        }

        $queries[] = [
            'connection' => $query->connectionName,
            'time_ms' => round($query->time, 2),
            'sql' => method_exists($query, 'toRawSql') ? $query->toRawSql() : $query->sql,
        ];
    });

    foreach (userDogsViewActionDiagnosticSamples() as $label => $sample) {
        $dog = PrevDog::query()
            ->whereKey($sample['dog_id'])
            ->where('SagirID', $sample['sagir_id'])
            ->first();

        if (! $dog instanceof PrevDog) {
            test()->markTestSkipped("Sample dog {$sample['dog_id']} / {$sample['sagir_id']} was not found.");
        }

        $component = livewire(UserDogsTable::class);
        $queries = [];

        $startedAt = microtime(true);
        $captureQueries = true;

        try {
            $component
                ->mountAction(TestAction::make('view')->table($dog))
                ->assertMountedActionModalSee((string) $sample['sagir_id']);
        } finally {
            $captureQueries = false;
        }

        $profile = userDogsViewActionDiagnosticSummariseQueries($queries);
        $profile['elapsed_ms'] = round((microtime(true) - $startedAt) * 1000, 2);
        $profile['dog'] = [
            'id' => $sample['dog_id'],
            'SagirID' => $sample['sagir_id'],
            'full_name' => $dog->full_name,
        ];

        $profiles[$label] = $profile;
    }

    $payload = [
        'generated_at' => now()->toIso8601String(),
        'purpose' => 'Livewire/Filament table view action modal query timing for /user/dogs.',
        'prev_user_id' => 258298,
        'samples' => $profiles,
        'index_state' => userDogsViewActionDiagnosticIndexState(),
    ];

    userDogsViewActionDiagnosticWriteArtifact($payload);

    $lazyTitleLookupCounts = collect($profiles)
        ->mapWithKeys(fn (array $profile, string $label): array => [
            $label => $profile['groups']['titles.lazy_title_lookup']['query_count'] ?? 0,
        ])
        ->filter(fn (int $count): bool => $count > 1)
        ->all();

    expect($lazyTitleLookupCounts)
        ->toBe([], 'Repeated lazy PrevTitle lookups were detected. Review '.userDogsViewActionDiagnosticArtifactPath().' for the grouped query profile.');
})->group('diagnostic', 'filament', 'livewire');
