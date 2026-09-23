<?php

namespace App\Livewire\Concerns;

use App\Models\Shop;
use App\Models\Warehouse;
use Livewire\Attributes\Computed;
use Spatie\Permission\Models\Role;

trait HasEmployeeFormData
{
    public array $departments = [
        'sales',
        'inventory',
        'purchasing',
        'supplier',
    ];

    public array $positions = [
        'sales associate',
        'inventory clerk',
        'purchasing coordinator',
        'supplier relationship manager',
    ];

    #[Computed]
    public function shops()
    {
        return Shop::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function warehouses()
    {
        return Warehouse::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->where('guard_name', 'employee')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function updated($property): void
    {
        if ($property === 'form.department') {
            $this->form->shop_id = null;
            $this->form->warehouse_id = null;
        }
    }
}