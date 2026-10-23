<?php

namespace App\Livewire;

use App\Services\ReportsService;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public string $fromDate;

    public string $toDate;

    /** @var array<string, int> */
    public array $tablePages = [
        'dailySales' => 1,
        'topProducts' => 1,
        'lowProducts' => 1,
        'payments' => 1,
        'installments' => 1,
        'employees' => 1,
        'suppliers' => 1,
    ];

    private const PAGINATED_REPORTS = [
        'dailySales' => 'sales_by_day',
        'topProducts' => 'top_products',
        'lowProducts' => 'low_products',
        'payments' => 'installment_payments',
        'installments' => 'open_installments',
        'employees' => 'sales_by_employee',
        'suppliers' => 'purchases_by_supplier',
    ];

    public function mount(): void
    {
        $this->fromDate = now()->subDays(29)->toDateString();
        $this->toDate = now()->toDateString();
    }

    protected function rules(): array
    {
        return [
            'fromDate' => ['required', 'date'],
            'toDate' => ['required', 'date', 'after_or_equal:fromDate'],
        ];
    }

    #[Computed]
    public function report(): array
    {
        return app(ReportsService::class)->dashboard($this->fromDate, $this->toDate);
    }

    #[Computed]
    public function chartData(): array
    {
        $report = $this->report;
        $salesByDay = collect($report['sales_by_day'] ?? []);
        $products = collect($report['top_products'] ?? [])->take(8);
        $paymentsByDay = collect($report['payments_by_day'] ?? []);
        $paymentMethods = collect($report['payment_methods'] ?? []);

        return [
            'sales' => [
                'labels' => $salesByDay->pluck('date')->values()->all(),
                'revenue' => $salesByDay->pluck('revenue')->map(fn ($value) => (float) $value)->values()->all(),
                'orders' => $salesByDay->pluck('orders')->map(fn ($value) => (int) $value)->values()->all(),
            ],
            'products' => [
                'labels' => $products->pluck('name')->values()->all(),
                'quantity' => $products->pluck('sold_quantity')->map(fn ($value) => (int) $value)->values()->all(),
            ],
            'paymentsByDay' => [
                'labels' => $paymentsByDay->pluck('date')->values()->all(),
                'totals' => $paymentsByDay->pluck('total')->map(fn ($value) => (float) $value)->values()->all(),
            ],
            'payments' => [
                'labels' => $paymentMethods->pluck('method')->map(fn ($value) => ucfirst(str_replace('_', ' ', (string) $value)))->values()->all(),
                'totals' => $paymentMethods->pluck('total')->map(fn ($value) => (float) $value)->values()->all(),
            ],
        ];
    }

    public function paginatedRows(string $table, int $perPage = 5): LengthAwarePaginator
    {
        abort_unless(array_key_exists($table, self::PAGINATED_REPORTS), 404);

        $reportKey = self::PAGINATED_REPORTS[$table];
        $rows = collect($this->report[$reportKey] ?? [])->values();
        $perPage = max(1, min($perPage, 25));
        $lastPage = max(1, (int) ceil($rows->count() / $perPage));
        $currentPage = max(1, min((int) ($this->tablePages[$table] ?? 1), $lastPage));
        $this->tablePages[$table] = $currentPage;

        return new LengthAwarePaginator(
            $rows->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $rows->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => $table],
        );
    }

    public function changePage(string $table, int $direction): void
    {
        abort_unless(array_key_exists($table, self::PAGINATED_REPORTS), 404);
        abort_unless(in_array($direction, [-1, 1], true), 404);

        $paginator = $this->paginatedRows($table);
        $this->tablePages[$table] = max(1, min($paginator->lastPage(), $paginator->currentPage() + $direction));
    }

    public function refreshDashboard(): void
    {
        $this->validate();

        unset($this->report, $this->chartData);
        foreach ($this->tablePages as $table => $page) {
            $this->tablePages[$table] = 1;
        }

        $this->dispatch('dashboard-charts-updated', chartData: $this->chartData);
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
