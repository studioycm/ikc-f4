@props([
    'panelName',
    'imageUrl',
    'darkImageUrl' => null,
    'imageAlt' => null,
    'heading' => null,
    'description' => null,
    'switchUrl' => null,
    'switchLabel' => null,
])

<div class="mb-8 flex flex-col items-center gap-4 text-center">
    @if ($displayImage)
        <div class="flex w-full items-center justify-center">
            <img
                src="{{ $imageUrl }}"
                alt="{{ $imageAlt ?? $heading ?? $panelName }}"
                class="{{ $darkImageUrl ? 'dark:hidden' : '' }} h-20 w-20 object-contain"
                loading="eager"
            >

            @if ($darkImageUrl)
                <img
                    src="{{ $darkImageUrl }}"
                    alt="{{ $imageAlt ?? $heading ?? $panelName }}"
                    class="hidden h-20 w-20 object-contain dark:block"
                    loading="eager"
                >
            @endif
        </div>
    @endif

    <div class="space-y-1">
        @if ($heading)
            <h1 class="text-xl font-semibold tracking-normal text-gray-950 dark:text-white">
                {{ $heading }}
            </h1>
        @endif

        @if ($panelName)
            <p class="text-sm font-medium text-primary-600 dark:text-primary-400">
                {{ $panelName }}
            </p>
        @endif

        @if ($description)
            <p class="text-sm leading-6 text-gray-600 dark:text-gray-400">
                {{ $description }}
            </p>
        @endif
    </div>

    @if ($switchUrl && $switchLabel)
        <a
            href="{{ $switchUrl }}"
            class="inline-flex items-center justify-center rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-primary-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 dark:border-white/10 dark:text-gray-200 dark:hover:bg-white/5"
        >
            {{ $switchLabel }}
        </a>
    @endif
</div>
