<?php

namespace App\Livewire\Concerns;

trait WithBulkSelection
{
    public array $selected = [];

    public bool $selectAll = false;

    /**
     * Handles a Select All checkbox for the current page only.
     * Selections from other pages are preserved when unchecking.
     */
    public function updatedSelectAll(mixed $checked): void
    {
        $this->selectAll = $this->toBoolean($checked);
        $this->setBulkSelectionFromSelectAll($this->selectAll);
    }

    public function updatedSelected(mixed $value = null): void
    {
        $this->syncBulkSelection();
    }

    public function updatedPage(): void
    {
        $this->syncBulkSelection();
    }

    public function clearBulkSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    abstract public function getBulkVisibleIds(): array;

    protected function setBulkSelectionFromSelectAll(mixed $checked): void
    {
        $visibleIds = $this->normalizeBulkIds($this->getBulkVisibleIds());
        $selectedIds = $this->normalizeBulkIds($this->selected);

        if ($this->toBoolean($checked)) {
            $this->selected = array_values(array_unique([
                ...$selectedIds,
                ...$visibleIds,
            ]));
        } else {
            // Uncheck only the IDs on this page. Keep selections from other pages.
            $this->selected = array_values(array_diff($selectedIds, $visibleIds));
        }

        $this->syncBulkSelection($visibleIds);
    }

    protected function syncBulkSelection(?array $visibleIds = null): void
    {
        $visibleIds ??= $this->getBulkVisibleIds();
        $visibleIds = $this->normalizeBulkIds($visibleIds);
        $selectedIds = $this->normalizeBulkIds($this->selected);

        // Normalise the selected IDs so checkbox values remain consistent.
        $this->selected = $selectedIds;

        $this->selectAll = $visibleIds !== []
            && count(array_intersect($visibleIds, $selectedIds)) === count($visibleIds);
    }

    protected function normalizeBulkIds(array $ids): array
    {
        return array_values(array_unique(array_filter(
            array_map('intval', $ids),
            static fn (int $id): bool => $id > 0
        )));
    }

    protected function toBoolean(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
