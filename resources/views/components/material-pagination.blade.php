@props(['pagination'])

<div class="flex flex-col items-center">
    {{-- Help text --}}
    <span class="text-sm text-gray-700 dark:text-gray-400">
        Page
        <span class="font-semibold text-gray-900 dark:text-white">
            {{ $pagination->currentPage() }}
        </span>
        of
        <span class="font-semibold text-gray-900 dark:text-white">
            {{ $pagination->lastPage() }}
        </span>
    </span>

    {{-- Buttons --}}
    <div class="inline-flex mt-2 xs:mt-0">
        @if ($pagination->onFirstPage() === false)
            <a href="{{ $pagination->previousPageUrl() }}"
               class="flex items-center justify-center px-4 h-10 text-base font-medium text-white bg-gray-800 rounded-s hover:bg-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                ← Previous
            </a>
        @endif

        @if ($pagination->hasMorePages())
            <a href="{{ $pagination->nextPageUrl() }}"
               class="flex items-center justify-center px-4 h-10 text-base font-medium text-white bg-gray-800 border-l border-gray-700 rounded-e hover:bg-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                Next →
            </a>
        @endif
    </div>
</div>