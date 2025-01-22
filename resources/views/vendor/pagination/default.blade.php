@if ($paginator->hasPages())

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <a href="javascript:void(0);" class="page-link">Previous</a>
            </li>
        @else
            <li class="page-item">
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-link">Previous</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><a class="page-link" href="javascript:void(0);">{{ $page }}</a></li>
                    @else
                        <li class="page-item"><a href="{{ $url }}" class="page-link">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true">
                <a class="page-link" href="javascript:void(0);">Next</a>
            </li>
        @endif
    </ul>
</nav>
@endif
