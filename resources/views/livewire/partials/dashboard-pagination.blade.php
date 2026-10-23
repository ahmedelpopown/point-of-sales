@if ($paginator->lastPage() > 1)
    <nav class="pd-pagination" aria-label="Table pagination">
        <p class="pd-pagination-info">
            Showing {{ $paginator->total() > 0 ? $paginator->firstItem() : 0 }}–{{ $paginator->lastItem() ?? 0 }} of {{ number_format($paginator->total()) }}
        </p>
        <div class="pd-pagination-controls">
            <button type="button" wire:click="changePage('{{ $table }}', -1)" @disabled($paginator->onFirstPage()) class="pd-page-btn">Previous</button>
            <span class="pd-page-number">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
            <button type="button" wire:click="changePage('{{ $table }}', 1)" @disabled(!$paginator->hasMorePages()) class="pd-page-btn">Next</button>
        </div>
    </nav>
@endif
