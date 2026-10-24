<?php

use App\Livewire\Concerns\WithBulkSelection;

test('select all only toggles visible records while preserving other page selections', function () {
    $component = new class
    {
        use WithBulkSelection;

        public array $visibleIds = [1, 2];

        public function getBulkVisibleIds(): array
        {
            return $this->visibleIds;
        }
    };

    $component->updatedSelectAll(true);

    expect($component->selected)->toBe([1, 2])
        ->and($component->selectAll)->toBeTrue();

    $component->visibleIds = [3, 4];
    $component->updatedPage();

    expect($component->selected)->toBe([1, 2])
        ->and($component->selectAll)->toBeFalse();

    $component->updatedSelectAll(true);

    expect($component->selected)->toBe([1, 2, 3, 4])
        ->and($component->selectAll)->toBeTrue();

    $component->updatedSelectAll(false);

    expect($component->selected)->toBe([1, 2])
        ->and($component->selectAll)->toBeFalse();
});

test('selection changes synchronize select all with the current page', function () {
    $component = new class
    {
        use WithBulkSelection;

        public array $visibleIds = [5, 6];

        public function getBulkVisibleIds(): array
        {
            return $this->visibleIds;
        }
    };

    $component->selected = ['5', '6'];
    $component->updatedSelected();

    expect($component->selected)->toBe([5, 6])
        ->and($component->selectAll)->toBeTrue();

    $component->clearBulkSelection();

    expect($component->selected)->toBe([])
        ->and($component->selectAll)->toBeFalse();
});
