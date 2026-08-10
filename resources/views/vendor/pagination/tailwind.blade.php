@if ($paginator->hasPages())
    @php
        $pageClass = 'inline-flex min-h-10 min-w-10 items-center justify-center border border-blue-500 bg-white px-3 py-2 text-sm font-medium text-blue-700 transition hover:bg-gray-500/5 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-200';
        $disabledClass = 'inline-flex min-h-10 min-w-10 cursor-not-allowed items-center justify-center border border-blue-300 bg-white px-3 py-2 text-sm font-medium text-blue-300 opacity-60';
        $activeClass = 'relative z-10 inline-flex min-h-10 min-w-10 cursor-default items-center justify-center border border-blue-600 bg-white px-3 py-2 text-sm font-bold text-blue-700 ring-1 ring-inset ring-blue-600';
    @endphp

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="flex items-center justify-between gap-3 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="{{ $disabledClass }} rounded-lg" aria-disabled="true">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $pageClass }} rounded-lg">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $pageClass }} rounded-lg">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="{{ $disabledClass }} rounded-lg" aria-disabled="true">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden items-center justify-between gap-4 sm:flex">
            <p class="text-sm leading-5 text-gray-600">
                {!! __('Showing') !!}
                @if ($paginator->firstItem())
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                {!! __('of') !!}
                <span class="font-medium">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>

            <div class="inline-flex overflow-hidden rounded-lg shadow-sm">
                @if ($paginator->onFirstPage())
                    <span class="{{ $disabledClass }} rounded-l-lg" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $pageClass }} rounded-l-lg" aria-label="{{ __('pagination.previous') }}">
                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="{{ $disabledClass }} -ml-px" aria-disabled="true">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="{{ $activeClass }} -ml-px" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $pageClass }} -ml-px" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $pageClass }} -ml-px rounded-r-lg" aria-label="{{ __('pagination.next') }}">
                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    </a>
                @else
                    <span class="{{ $disabledClass }} -ml-px rounded-r-lg" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif