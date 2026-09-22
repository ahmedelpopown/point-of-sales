<?php

use App\Enums\StockMovementType;
use App\Models\Employee;
use App\Models\Shop;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Stock Movements')] class extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $locationType = '';

    #[Url(except: '')]
    public string $locationId = '';

    #[Url(except: '')]
    public string $movementType = '';

    #[Url(except: '')]
    public string $employeeId = '';

    #[Url(except: '')]
    public string $dateFrom = '';

    #[Url(except: '')]
    public string $dateTo = '';

    #[Url(except: 'created_at')]
    public string $sortField = 'created_at';

    #[Url(except: 'desc')]
    public string $sortDirection = 'desc';

    public int $perPage = 15;

    #[Computed]
    public function movementTypes(): array
    {
        return collect(StockMovementType::cases())
            ->mapWithKeys(fn (StockMovementType $type) => [
                $type->value => Str::headline(
                    strtolower($type->name)
                ),
            ])
            ->all();
    }

    #[Computed]
    public function warehouses()
    {
        return Warehouse::where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function shops()
    {
        return Shop::where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function employees()
    {
        return Employee::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
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
            ]);

        $this->applyFilters($query);

        return $query
            ->orderBy(
                $sortFields[$this->sortField] ?? $sortFields['created_at'],
                $this->sortDirection
            )
            ->paginate($this->perPage);
    }

    private function applyFilters(Builder $query): void
    {
        $query
            ->when(
                $this->search,
                function (Builder $query) {
                    $search = "%{$this->search}%";

                    $query->where(function (Builder $query) use ($search) {
                        $query
                            ->where('products.name', 'like', $search)
                            ->orWhere('products.barcode', 'like', $search);
                    });
                }
            )
            ->when(
                $this->locationType,
                function (Builder $query) {
                    $model = $this->locationType === 'warehouse'
                        ? Warehouse::class
                        : Shop::class;

                    $query->whereHas('stock', function (Builder $query) use ($model) {
                        $query
                            ->where('stockable_type', $model)
                            ->when(
                                $this->locationId,
                                fn (Builder $query) => $query->where(
                                    'stockable_id',
                                    $this->locationId
                                )
                            );
                    });
                }
            )
            ->when(
                $this->movementType,
                fn (Builder $query) => $query->where(
                    'stock_movements.type',
                    $this->movementType
                )
            )
            ->when(
                $this->employeeId,
                fn (Builder $query) => $query->where(
                    'stock_movements.employee_id',
                    $this->employeeId
                )
            )
            ->when(
                $this->dateFrom,
                fn (Builder $query) => $query->whereDate(
                    'stock_movements.created_at',
                    '>=',
                    $this->dateFrom
                )
            )
            ->when(
                $this->dateTo,
                fn (Builder $query) => $query->whereDate(
                    'stock_movements.created_at',
                    '<=',
                    $this->dateTo
                )
            );
    }

    #[Computed]
    public function statistics(): array
    {
        $incoming = [
            'PURCHASE',
            'TRANSFER_IN',
            'ADJUSTMENT_IN',
            'SALE_REVERSAL',
        ];

        $outgoing = [
            'SALE',
            'TRANSFER_OUT',
            'ADJUSTMENT_OUT',
            'PURCHASE_REVERSAL',
        ];

        $types = collect(StockMovementType::cases())
            ->keyBy(fn (StockMovementType $type) => $type->name);

        $incomingTypes = collect($incoming)
            ->map(fn ($name) => $types[$name]?->value)
            ->filter()
            ->all();

        $outgoingTypes = collect($outgoing)
            ->map(fn ($name) => $types[$name]?->value)
            ->filter()
            ->all();

        return [
            'total_movements' => StockMovement::count(),

            'incoming_quantity' => StockMovement::whereIn(
                'type',
                $incomingTypes
            )->sum('quantity'),

            'outgoing_quantity' => StockMovement::whereIn(
                'type',
                $outgoingTypes
            )->sum('quantity'),

            'today_movements' => StockMovement::whereDate(
                'created_at',
                today()
            )->count(),
        ];
    }

    public function sortBy(string $field): void
    {
        $allowed = [
            'product',
            'quantity',
            'type',
            'created_at',
        ];

        if (! in_array($field, $allowed, true)) {
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

    public function updated($property): void
    {
        if (in_array($property, [
            'search',
            'locationType',
            'locationId',
            'movementType',
            'employeeId',
            'dateFrom',
            'dateTo',
        ], true)) {
            $this->resetPage();
        }

        if ($property === 'locationType') {
            $this->locationId = '';
        }
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
        return $movement->stock?->stockable?->name ?? 'Unknown';
    }
};
