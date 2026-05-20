<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogFilamentActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (auth()->check() && str_starts_with($request->path(), 'admin')) {

            if ($this->shouldLog($request)) {
                $event = $this->determineEvent($request);
                $pageName = $this->getPageName($request);

                // Store page name in properties, NOT in subject_type
                $properties = [
                    'page' => $pageName, // <--- Saving it here is safe
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                    'method' => $request->method(),
                    'agent' => $request->userAgent(),
                    'query' => $request->query(),
                ];

                if ($request->isMethod('post') || $request->isMethod('put')) {
                    $properties['payload'] = $request->except(['password', '_token', 'components']);
                }

                activity()
                    ->causedBy(auth()->user())
                    ->event($event)
                    ->withProperties($properties)
                    ->log("User {$event} {$pageName}");
            }
        }

        return $response;
    }

    protected function shouldLog(Request $request): bool
    {
        if (str_contains($request->path(), 'livewire/update')) {
            return false;
        }

        return $request->isMethod('get') || $request->hasHeader('X-Livewire');
    }

    protected function determineEvent(Request $request): string
    {
        if ($request->isMethod('get')) {
            return filled($request->query()) ? 'searched' : 'viewed';
        }

        return strtolower($request->method());
    }

    protected function getPageName(Request $request): string
    {
        $name = $request->route()?->getName();

        if (! $name) {
            return 'Dashboard';
        }

        return Str::of($name)
            // Use replaceMatches() for regex replacement
            ->replaceMatches('/^filament\.[^.]+\./', '') // Remove 'filament.admin.'
            ->replace(['resources.', 'pages.'], '')      // Remove common sub-namespaces
            ->beforeLast('.')                             // Remove '.index', '.edit'
            ->replace('.', ' ')                           // Replace remaining dots with spaces
            ->title()                                     // Capitalize "Shop Orders"
            ->toString();
    }
}
