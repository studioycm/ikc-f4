<?php

use App\Models\User;
use Illuminate\Support\Facades\File;

function userDogsBrowserDiagnosticSamples(): array
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

function userDogsBrowserDiagnosticArtifactPath(): string
{
    return storage_path('app/testing/user-dogs-view-action-browser-profile.json');
}

function userDogsBrowserDiagnosticWriteArtifact(array $payload): void
{
    $path = userDogsBrowserDiagnosticArtifactPath();

    File::ensureDirectoryExists(dirname($path));
    File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function userDogsBrowserDiagnosticViewActionSelector(): string
{
    return '[wire\\:click*="mountAction(\'view\'"]';
}

function userDogsBrowserDiagnosticTableSearchSelector(): string
{
    return '.fi-ta-search-field input[type="search"]';
}

function userDogsBrowserDiagnosticModalStatusScript(int $sagirId): string
{
    $script = <<<'JS'
(() => {
    const sagirId = __SAGIR_ID__;
    const bodyText = document.body?.innerText || '';
    const dialogs = Array.from(document.querySelectorAll('[role="dialog"], .fi-modal-window'));
    const visibleDialog = dialogs.find((element) => {
        const style = window.getComputedStyle(element);

        return style.display !== 'none'
            && style.visibility !== 'hidden'
            && element.getClientRects().length > 0;
    });
    const modalText = visibleDialog ? visibleDialog.innerText : '';

    return {
        modal_open: Boolean(visibleDialog),
        modal_contains_sagir: modalText.includes(sagirId),
        modal_excerpt: modalText.slice(0, 2000),
        has_max_execution_error: bodyText.includes('Maximum execution time of 30 seconds exceeded'),
        has_internal_server_error: bodyText.includes('Internal Server Error'),
        body_excerpt: bodyText.slice(0, 2000),
    };
})()
JS;

    return strtr($script, [
        '__SAGIR_ID__' => json_encode((string) $sagirId),
    ]);
}

function userDogsBrowserDiagnosticResourceTimingsScript(): string
{
    return <<<'JS'
(() => {
    return performance
        .getEntriesByType('resource')
        .filter((entry) => entry.name.includes('/livewire/update'))
        .map((entry) => ({
            name: entry.name,
            start_time_ms: Math.round(entry.startTime * 100) / 100,
            duration_ms: Math.round(entry.duration * 100) / 100,
            transfer_size: entry.transferSize,
            encoded_body_size: entry.encodedBodySize,
            decoded_body_size: entry.decodedBodySize,
        }));
})()
JS;
}

function userDogsBrowserDiagnosticRowInspectionScript(int $sagirId): string
{
    $script = <<<'JS'
(() => {
    const sagirId = __SAGIR_ID__;
    const rows = Array.from(document.querySelectorAll('tr'));
    const row = rows.find((element) => (element.innerText || '').includes(sagirId));
    const actionCandidates = Array.from(document.querySelectorAll('button, a'))
        .map((element) => ({
            text: (element.textContent || '').trim(),
            aria_label: element.getAttribute('aria-label'),
            title: element.getAttribute('title'),
            wire_click: element.getAttribute('wire:click'),
            wire_key: element.getAttribute('wire:key'),
        }))
        .filter((element) => element.wire_click || element.aria_label || element.title || element.text);

    return {
        row_found: Boolean(row),
        row_excerpt: row ? (row.innerText || '').slice(0, 2000) : null,
        row_actions: row
            ? Array.from(row.querySelectorAll('button, a')).map((element) => ({
                text: (element.textContent || '').trim(),
                aria_label: element.getAttribute('aria-label'),
                title: element.getAttribute('title'),
                wire_click: element.getAttribute('wire:click'),
                wire_key: element.getAttribute('wire:key'),
            }))
            : [],
        global_action_candidates: actionCandidates
            .filter((element) => (element.wire_click || '').includes('Action') || (element.aria_label || '').length || (element.title || '').length)
            .slice(0, 30),
    };
})()
JS;

    return strtr($script, [
        '__SAGIR_ID__' => json_encode((string) $sagirId),
    ]);
}

function userDogsBrowserDiagnosticPageText(string $html): string
{
    return preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5)) ?? '';
}

function userDogsBrowserDiagnosticClassifyQuery(string $sql): string
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

function userDogsBrowserDiagnosticExtractErrorQueries(string $html): array
{
    $text = userDogsBrowserDiagnosticPageText($html);

    preg_match_all(
        '/\*\s+(mysql(?:_prev)?)\s+-\s+(.+?)\s+\(([\d.]+)\s+ms\)/',
        $text,
        $matches,
        PREG_SET_ORDER,
    );

    $queries = [];

    foreach ($matches as $match) {
        $sql = trim($match[2]);

        $queries[] = [
            'connection' => $match[1],
            'time_ms' => round((float) $match[3], 2),
            'group' => userDogsBrowserDiagnosticClassifyQuery($sql),
            'sql' => $sql,
        ];
    }

    return $queries;
}

test('browser profiles user dogs view action modal timing and livewire update failures', function () {
    set_time_limit(180);

    $sampleUser = User::query()
        ->where('email', BROWSER_SUPER_ADMIN_EMAIL)
        ->first();

    if (! $sampleUser instanceof User || (int) $sampleUser->prev_user_id !== 258298) {
        test()->markTestSkipped('Browser diagnostic user is not linked to PrevUser id 258298.');
    }

    $page = browserLogin('user', BROWSER_SUPER_ADMIN_EMAIL, BROWSER_SUPER_ADMIN_PASSWORD);
    $page->assertPathIs('/user');

    $reports = [];

    foreach (userDogsBrowserDiagnosticSamples() as $label => $sample) {
        $page->navigate('/user/dogs');
        $page->wait(3);
        $page->assertNoJavaScriptErrors();
        $page->clear(userDogsBrowserDiagnosticTableSearchSelector());
        $page->type(userDogsBrowserDiagnosticTableSearchSelector(), (string) $sample['sagir_id']);
        $page->wait(3);
        $page->assertSee((string) $sample['sagir_id']);

        $startedAt = microtime(true);
        $selector = userDogsBrowserDiagnosticViewActionSelector();
        $clickResult = [
            'found' => false,
            'selector' => $selector,
            'label' => null,
            'wire_click' => null,
        ];

        if ((int) $page->script(sprintf(
            'document.querySelectorAll(%s).length',
            json_encode($selector),
        )) > 0) {
            $clickResult = [
                'found' => true,
                'selector' => $selector,
                'label' => trim((string) ($page->text($selector) ?? '')),
                'wire_click' => $page->attribute($selector, 'wire:click'),
            ];

            $page->script('window.performance.clearResourceTimings()');
            $page->click($selector);
        }

        $status = [
            'modal_open' => false,
            'modal_contains_sagir' => false,
            'has_max_execution_error' => false,
            'has_internal_server_error' => false,
            'body_excerpt' => '',
            'modal_excerpt' => '',
        ];

        if (($clickResult['found'] ?? false) === true) {
            for ($second = 0; $second < 35; $second++) {
                $page->wait(1);

                $status = $page->script(userDogsBrowserDiagnosticModalStatusScript($sample['sagir_id']));

                if (
                    ($status['modal_open'] ?? false) === true
                    && ($status['modal_contains_sagir'] ?? false) === true
                ) {
                    $page->assertNoJavaScriptErrors();
                    break;
                }

                if (
                    ($status['has_max_execution_error'] ?? false) === true
                    || ($status['has_internal_server_error'] ?? false) === true
                ) {
                    break;
                }
            }
        }

        $html = $page->content();
        $queries = userDogsBrowserDiagnosticExtractErrorQueries($html);

        $reports[$label] = [
            'dog' => $sample,
            'elapsed_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            'click' => $clickResult,
            'status' => $status,
            'row_inspection' => $page->script(userDogsBrowserDiagnosticRowInspectionScript($sample['sagir_id'])),
            'livewire_update_resource_timings' => $page->script(userDogsBrowserDiagnosticResourceTimingsScript()),
            'error_query_count' => count($queries),
            'error_query_groups' => collect($queries)
                ->groupBy('group')
                ->map(fn ($group): array => [
                    'query_count' => $group->count(),
                    'total_time_ms' => round((float) $group->sum('time_ms'), 2),
                    'examples' => $group->pluck('sql')->take(5)->values()->all(),
                ])
                ->sortByDesc('total_time_ms')
                ->all(),
            'error_queries' => $queries,
        ];
    }

    $payload = [
        'generated_at' => now()->toIso8601String(),
        'purpose' => 'Real browser timing for Filament table view action modal on /user/dogs.',
        'prev_user_id' => 258298,
        'samples' => $reports,
    ];

    userDogsBrowserDiagnosticWriteArtifact($payload);

    $failedSamples = collect($reports)
        ->filter(function (array $report): bool {
            $status = $report['status'];

            return ($report['click']['found'] ?? false) !== true
                || ($status['modal_open'] ?? false) !== true
                || ($status['modal_contains_sagir'] ?? false) !== true
                || ($status['has_max_execution_error'] ?? false) === true
                || ($status['has_internal_server_error'] ?? false) === true;
        })
        ->keys()
        ->all();

    expect($failedSamples)
        ->toBe([], 'One or more browser modal opens failed or timed out. Review '.userDogsBrowserDiagnosticArtifactPath().' for timings and any Symfony query dump.');
})->group('diagnostic', 'browser', 'filament');
