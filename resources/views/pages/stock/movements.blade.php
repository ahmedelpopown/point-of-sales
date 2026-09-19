<?php

use App\Enums\StockMovementType;
use App\Models\Employee;
use App\Models\Shop;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Stock Movements')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $locationType = '';

    public string $locationId = '';

    public string $movementType = '';

    public string $employeeId = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'locationType' => ['except' => ''],
        'locationId' => ['except' => ''],
        'movementType' => ['except' => ''],
        'employeeId' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    #[Computed]
    public function movementTypes(): array
    {
        return collect(StockMovementType::cases())
            ->mapWithKeys(function (StockMovementType $type) {
                return [
                    $type->value => Str::headline(
                        strtolower($type->name)
                    ),
                ];
            })
            ->all();
    }

    #[Computed]
    public function warehouses()
    {
        return Warehouse::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function shops()
    {
        return Shop::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function employees()
    {
        return Employee::query()
            ->where('status', 'active')
            ->orderBy('id')
            ->get();
    }

    #[Computed]
    public function movements()
    {
        $sortFields = [
            'product' => 'products.name',
            'quantity' => 'stock_movements.quantity',
            'type' => 'stock_movements.type',
            'created_at' => 'stock_movements.created_at',
        ];

        $query = StockMovement::query()
            ->join(
                'products',
                'products.id',
                '=',
                'stock_movements.product_id'
            )
            ->select('stock_movements.*')
            ->with([
                'product',
                'stock.stockable',
                'employee',
                'reference',
            ])
            ->when($this->search, function (Builder $query) {
                $search = "%{$this->search}%";

                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where(
                            'products.name',
                            'like',
                            $search
                        )
                        ->orWhere(
                            'products.barcode',
                            'like',
                            $search
                        );
                });
            })
            ->when(
                $this->locationType === 'warehouse',
                fn(Builder $query) => $query->whereHas(
                    'stock',
                    fn(Builder $query) => $query
                        ->where(
                            'stockable_type',
                            Warehouse::class
                        )
                        ->when(
                            $this->locationId,
                            fn(Builder $query) => $query->where(
                                'stockable_id',
                                $this->locationId
                            )
                        )
                )
            )
            ->when(
                $this->locationType === 'shop',
                fn(Builder $query) => $query->whereHas(
                    'stock',
                    fn(Builder $query) => $query
                        ->where(
                            'stockable_type',
                            Shop::class
                        )
                        ->when(
                            $this->locationId,
                            fn(Builder $query) => $query->where(
                                'stockable_id',
                                $this->locationId
                            )
                        )
                )
            )
            ->when(
                $this->movementType,
                fn(Builder $query) => $query->where(
                    'stock_movements.type',
                    $this->movementType
                )
            )
            ->when(
                $this->employeeId,
                fn(Builder $query) => $query->where(
                    'stock_movements.employee_id',
                    $this->employeeId
                )
            )
            ->when(
                $this->dateFrom,
                fn(Builder $query) => $query->whereDate(
                    'stock_movements.created_at',
                    '>=',
                    $this->dateFrom
                )
            )
            ->when(
                $this->dateTo,
                fn(Builder $query) => $query->whereDate(
                    'stock_movements.created_at',
                    '<=',
                    $this->dateTo
                )
            );

        $sortColumn = $sortFields[$this->sortField]
            ?? $sortFields['created_at'];

        return $query
            ->orderBy(
                $sortColumn,
                $this->sortDirection
            )
            ->paginate($this->perPage);
    }

#[Computed]
public function statistics(): array
{
    $base = StockMovement::query();

    $incomingTypes = collect(StockMovementType::cases())
        ->filter(fn (StockMovementType $type) => in_array(
            $type->name,
            [
                'PURCHASE',
                'TRANSFER_IN',
                'ADJUSTMENT_IN',
                'SALE_REVERSAL',
            ],
            true
        ))
        ->pluck('value')
        ->all();

    $outgoingTypes = collect(StockMovementType::cases())
        ->filter(fn (StockMovementType $type) => in_array(
            $type->name,
            [
                'SALE',
                'TRANSFER_OUT',
                'ADJUSTMENT_OUT',
                'PURCHASE_REVERSAL',
            ],
            true
        ))
        ->pluck('value')
        ->all();

    return [
        'total_movements' => (clone $base)->count(),

        'incoming_quantity' => (clone $base)
            ->whereIn('type', $incomingTypes)
            ->sum('quantity'),

        'outgoing_quantity' => (clone $base)
            ->whereIn('type', $outgoingTypes)
            ->sum('quantity'),

        'today_movements' => (clone $base)
            ->whereDate('created_at', today())
            ->count(),
    ];
}

    public function sortBy(string $field): void
    {
        $allowedFields = [
            'product',
            'quantity',
            'type',
            'created_at',
        ];

        if (! in_array($field, $allowedFields, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === 'asc'
                ? 'desc'
                : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection =
                $field === 'created_at'
                ? 'desc'
                : 'asc';
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLocationType(): void
    {
        $this->locationId = '';

        $this->resetPage();
    }

    public function updatedLocationId(): void
    {
        $this->resetPage();
    }

    public function updatedMovementType(): void
    {
        $this->resetPage();
    }

    public function updatedEmployeeId(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'locationType',
            'locationId',
            'movementType',
            'employeeId',
            'dateFrom',
            'dateTo',
        ]);

        $this->resetPage();
    }

    public function movementTypeName(StockMovement $movement): string
    {
        return Str::headline(
            strtolower(
                $movement->type?->name ?? 'unknown'
            )
        );
    }

    public function movementTypeClass(StockMovement $movement): string
    {
        return match ($movement->type?->name) {
            'PURCHASE',
            'TRANSFER_IN',
            'ADJUSTMENT_IN',
            'SALE_REVERSAL'
            => 'bg-green-100 text-green-800',

            'SALE',
            'TRANSFER_OUT',
            'ADJUSTMENT_OUT',
            'PURCHASE_REVERSAL'
            => 'bg-red-100 text-red-800',

            default
            => 'bg-gray-100 text-gray-800',
        };
    }

    public function locationName(StockMovement $movement): string
    {
        return $movement->stock?->stockable?->name
            ?? 'Unknown';
    }
};
?>

<div class="px-4 sm:px-6 lg:px-8">

    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">

            <h1 class="text-2xl font-semibold text-gray-900">
                Stock Movements
            </h1>

            <p class="mt-2 text-sm text-gray-700">
                Track stock purchases, sales, transfers and adjustments.
            </p>

        </div>
    </div>

    {{-- Statistics --}}
    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Total Movements
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['total_movements']) }}
            </p>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Incoming Quantity
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['incoming_quantity']) }}
            </p>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Outgoing Quantity
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['outgoing_quantity']) }}
            </p>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Today's Movements
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['today_movements']) }}
            </p>
        </div>

    </div>

    {{-- Filters --}}
    <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Search product..."
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">

        <select
            wire:model.live="locationType"
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">All Locations</option>
            <option value="warehouse">Warehouses</option>
            <option value="shop">Shops</option>
        </select>

        @if($locationType === 'warehouse')

        <select
            wire:model.live="locationId"
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">All Warehouses</option>

            @foreach($this->warehouses as $warehouse)
            <option value="{{ $warehouse->id }}">
                {{ $warehouse->name }}
            </option>
            @endforeach
        </select>

        @elseif($locationType === 'shop')

        <select
            wire:model.live="locationId"
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">All Shops</option>

            @foreach($this->shops as $shop)
            <option value="{{ $shop->id }}">
                {{ $shop->name }}
            </option>
            @endforeach
        </select>

        @else

        <select
            disabled
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-400">
            <option>All Locations</option>
        </select>

        @endif

        <select
            wire:model.live="movementType"
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">All Movement Types</option>

            @foreach($this->movementTypes as $value => $label)
            <option value="{{ $value }}">
                {{ $label }}
            </option>
            @endforeach
        </select>

        <select
            wire:model.live="employeeId"
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">All Employees</option>

            @foreach($this->employees as $employee)
            <option value="{{ $employee->id }}">
                Employee #{{ $employee->id }}
            </option>
            @endforeach
        </select>

        <input
            wire:model.live="dateFrom"
            type="date"
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">

        <input
            wire:model.live="dateTo"
            type="date"
            class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">

        <button
            wire:click="resetFilters"
            type="button"
            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Reset
        </button>

    </div>

    {{-- Table --}}
    <div class="mt-8 flex flex-col">

        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">

            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">

                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">

                    <table class="min-w-full divide-y divide-gray-300">

                        <thead class="bg-gray-50">

                            <tr>

                                <th
                                    wire:click="sortBy('product')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Product
                                    @if($sortField === 'product')
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Location
                                </th>

                                <th
                                    wire:click="sortBy('type')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Type
                                    @if($sortField === 'type')
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th
                                    wire:click="sortBy('quantity')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Quantity
                                    @if($sortField === 'quantity')
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Before
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    After
                                </th>

                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Employee
                                </th>

                                <th
                                    wire:click="sortBy('created_at')"
                                    class="cursor-pointer px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Date
                                    @if($sortField === 'created_at')
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse($this->movements as $movement)

                            <tr
                                wire:key="movement-{{ $movement->id }}"
                                class="hover:bg-gray-50">

                                <td class="whitespace-nowrap px-3 py-4 text-sm">

                                    <div class="font-medium text-gray-900">
                                        {{ $movement->product->name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $movement->product->barcode }}
                                    </div>

                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $this->locationName($movement) }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm">

                                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $this->movementTypeClass($movement) }}">
                                        {{ $this->movementTypeName($movement) }}
                                    </span>

                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">
                                    {{ number_format($movement->quantity) }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ number_format($movement->quantity_before) }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">
                                    {{ number_format($movement->quantity_after) }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $movement->employee?->full_name ?? 'System' }}
                                </td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ Carbon::parse($movement->created_at)->format('Y-m-d H:i') }}
                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td
                                    colspan="8"
                                    class="px-3 py-8 text-center text-sm text-gray-500">
                                    No stock movements found.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <div class="mt-4">
        {{ $this->movements->links() }}
    </div>

</div>