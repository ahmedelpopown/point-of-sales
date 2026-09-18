<?php

use App\Services\ReportsService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Reports Dashboard')] class extends Component
{
    public string $fromDate;

    public string $toDate;

    public function mount(): void
    {
        $this->fromDate = now()
            ->subDays(89)
            ->format('Y-m-d');

        $this->toDate = now()
            ->format('Y-m-d');
    }

    #[Computed]
    public function report(): array
    {
        return app(ReportsService::class)->dashboard(
            $this->fromDate,
            $this->toDate
        );
    }

    #[Computed]
    public function chartData(): array
    {
        $report = $this->report;

        /*
        |--------------------------------------------------------------------------
        | Sales By Day
        |--------------------------------------------------------------------------
        */

        $salesByDay = collect(
            $report['sales_by_day'] ?? []
        );

        /*
        |--------------------------------------------------------------------------
        | Sales By Employee
        |--------------------------------------------------------------------------
        */

        $salesByEmployee = collect(
            $report['sales_by_employee'] ?? []
        );

        /*
        |--------------------------------------------------------------------------
        | Top Products
        |--------------------------------------------------------------------------
        */

        $topProducts = collect(
            $report['top_products'] ?? []
        );

        /*
        |--------------------------------------------------------------------------
        | Purchases By Supplier
        |--------------------------------------------------------------------------
        */

        $purchasesBySupplier = collect(
            $report['purchases_by_supplier'] ?? []
        );

        /*
        |--------------------------------------------------------------------------
        | Payment Methods
        |--------------------------------------------------------------------------
        */

        $paymentMethods = collect(
            $report['payment_methods'] ?? []
        );

        /*
        |--------------------------------------------------------------------------
        | Customers By Governorate
        |--------------------------------------------------------------------------
        */

        $customersByGovernorate = collect(
            $report['customers_by_governorate'] ?? []
        );

        return [

            /*
            |--------------------------------------------------------------------------
            | Sales Revenue Chart
            |--------------------------------------------------------------------------
            */

            'sales_revenue' => [
                'labels' => $salesByDay
                    ->pluck('date')
                    ->values()
                    ->all(),

                'data' => $salesByDay
                    ->pluck('revenue')
                    ->map(fn ($value) => (float) $value)
                    ->values()
                    ->all(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Sales Operations Chart
            |--------------------------------------------------------------------------
            */

            'sales_operations' => [
                'labels' => $salesByDay
                    ->pluck('date')
                    ->values()
                    ->all(),

                'data' => $salesByDay
                    ->pluck('orders')
                    ->map(fn ($value) => (int) $value)
                    ->values()
                    ->all(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Employee Sales Chart
            |--------------------------------------------------------------------------
            */

            'employee_sales' => [
                'labels' => $salesByEmployee
                    ->pluck('name')
                    ->values()
                    ->all(),

                'data' => $salesByEmployee
                    ->pluck('sales_revenue')
                    ->map(fn ($value) => (float) $value)
                    ->values()
                    ->all(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Top Products Chart
            |--------------------------------------------------------------------------
            */

            'top_products' => [
                'labels' => $topProducts
                    ->pluck('name')
                    ->values()
                    ->all(),

                'data' => $topProducts
                    ->pluck('sold_quantity')
                    ->map(fn ($value) => (int) $value)
                    ->values()
                    ->all(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Purchases By Supplier Chart
            |--------------------------------------------------------------------------
            */

            'supplier_purchases' => [
                'labels' => $purchasesBySupplier
                    ->pluck('name')
                    ->values()
                    ->all(),

                'data' => $purchasesBySupplier
                    ->pluck('purchase_total')
                    ->map(fn ($value) => (float) $value)
                    ->values()
                    ->all(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Payment Methods Chart
            |--------------------------------------------------------------------------
            */

            'payment_methods' => [
                'labels' => $paymentMethods
                    ->pluck('method')
                    ->map(fn ($value) => ucfirst((string) $value))
                    ->values()
                    ->all(),

                'data' => $paymentMethods
                    ->pluck('total')
                    ->map(fn ($value) => (float) $value)
                    ->values()
                    ->all(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Customers By Governorate Chart
            |--------------------------------------------------------------------------
            */

            'customers_by_governorate' => [
                'labels' => $customersByGovernorate
                    ->pluck('name')
                    ->values()
                    ->all(),

                'data' => $customersByGovernorate
                    ->pluck('customers')
                    ->map(fn ($value) => (int) $value)
                    ->values()
                    ->all(),
            ],

            /*
            |--------------------------------------------------------------------------
            | Installment Chart
            |--------------------------------------------------------------------------
            */

            'installments' => [
                'total' => (float) (
                    $report['installments']['total'] ?? 0
                ),

                'paid' => (float) (
                    $report['installments']['payments'] ?? 0
                ),

                'remaining' => (float) (
                    $report['installments']['remaining'] ?? 0
                ),
            ],
        ];
    }

    public function updatedFromDate(): void
    {
        $this->dispatch('reports-updated');
    }

    public function updatedToDate(): void
    {
        $this->dispatch('reports-updated');
    }

    public function refreshReports(): void
    {
        unset($this->report);
        unset($this->chartData);

        $this->dispatch('reports-updated');
    }
};