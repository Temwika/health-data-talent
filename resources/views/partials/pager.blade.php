@if ($paginator->hasPages())
    <nav class="pager" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="btn ghost small is-disabled" aria-disabled="true">Previous</span>
        @else
            <a class="btn ghost small" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
        @endif

        <span class="hint">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a class="btn ghost small" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
        @else
            <span class="btn ghost small is-disabled" aria-disabled="true">Next</span>
        @endif
    </nav>
@endif
