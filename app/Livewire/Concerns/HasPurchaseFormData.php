<?php

namespace App\Livewire\Concerns;

use App\Models\Employee;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use Livewire\Attributes\Computed;

trait HasPurchaseFormData
{
    public ?int $editingWarehouseId = null;

    #[Computed]
    public function suppliers()
    {
        return Supplier::query()
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function employees()
    {
        return Employee::query()
            ->orderBy('first_name')
            ->get();
    }

    #[Computed]
    public function warehouses()
    {
        return Warehouse::query()
            ->where('status', 'active')
            ->when(
                $this->editingWarehouseId,
                fn ($query) => $query->orWhere(
                    'id',
                    $this->editingWarehouseId
                )
            )
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->orderBy('name')
            ->get();
    }
}