@if ($paginator->hasPages())
    <nav
        class="custom-pagination"
        role="navigation"
        aria-label="Projects pagination"
    >
        <div class="pagination-info">
            Showing
            <strong>{{ $paginator->firstItem() }}</strong>
            to
            <strong>{{ $paginator->lastItem() }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
            projects
        </div>

        <div class="pagination-links">
            {{-- Previous page --}}
            @if ($paginator->onFirstPage())
                <span
                    class="page-button page-disabled"
                    aria-disabled="true"
                >
                    ‹
                </span>
            @else
                <a
                    class="page-button"
                    href="{{ $paginator->previousPageUrl() }}"
                    rel="prev"
                    aria-label="Previous page"
                >
                    ‹
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="page-dots">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <span
                                class="page-number page-active"
                                aria-current="page"
                            >
                                {{ $page }}
                            </span>
                        @else
                            <a
                                class="page-number"
                                href="{{ $url }}"
                                aria-label="Go to page {{ $page }}"
                            >
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next page --}}
            @if ($paginator->hasMorePages())
                <a
                    class="page-button"
                    href="{{ $paginator->nextPageUrl() }}"
                    rel="next"
                    aria-label="Next page"
                >
                    ›
                </a>
            @else
                <span
                    class="page-button page-disabled"
                    aria-disabled="true"
                >
                    ›
                </span>
            @endif
        </div>
    </nav>
@endif
