<?php

namespace App\Livewire\Concerns;

trait WithTableManagement
{
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === 'asc'
                    ? 'desc'
                    : 'asc';

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';

        $this->resetPage();
    }
}
