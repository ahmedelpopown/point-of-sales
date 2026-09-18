<?php

    namespace App\Services;

    use App\Models\Employee;
    use App\Models\Installment;
    use App\Models\Order;
    use App\Models\Payment;
    use App\Models\Product;
    use App\Models\Purchase;
    use App\Models\Supplier;
    use App\Models\User;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\DB;

    class ReportsService
    {
        public function dashboard(string $fromDate, string $toDate): array
        {
            $from = Carbon::parse($fromDate)->startOfDay();
            $to = Carbon::parse($toDate)->endOfDay();
            return ['summary' => $this->summary($from, $to), 'sales_by_day' => $this->salesByDay($from, $to), 'sales_by_employee' => $this->salesByEmployee($from, $to), 'top_products' => $this->topProducts($from, $to), 'purchases_by_supplier' => $this->purchasesBySupplier($from, $to), 'purchases_by_day' => $this->purchasesByDay($from, $to), 'payment_methods' => $this->paymentMethods($from, $to), 'installments' => $this->installmentReport($from, $to), 'customers_by_governorate' => $this->customersByGovernorate($from, $to), 'stock' => $this->stockReport(),];
        } /* |-------------------------------------------------------------------------- | Summary |-------------------------------------------------------------------------- */
        private function summary(Carbon $from, Carbon $to): array
        {
            $salesQuery = Order::query()->whereBetween('created_at', [$from, $to]);
            $purchaseQuery = Purchase::query()->whereBetween('created_at', [$from, $to]);
            $employees = Employee::count();
            $sellingEmployees = Employee::query()->whereHas('orders', function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })->count();
            $customers = User::query()->whereHas('orders', function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })->count();
            $salesRevenue = (float) $salesQuery->sum('total');
            $purchaseSpend = (float) $purchaseQuery->sum('total_price');
            $grossProfit = $this->grossProfit($from, $to);
            $paymentsCollected = (float) Payment::query()->whereBetween('payment_date', [$from->toDateString(), $to->toDateString(),])->sum('amount');
            $downPayments = (float) Installment::query()->whereBetween('start_date', [$from->toDateString(), $to->toDateString(),])->sum('down_payment');
            return ['sales_count' => $salesQuery->count(), 'sales_revenue' => $salesRevenue, 'purchase_count' => $purchaseQuery->count(), 'purchase_spend' => $purchaseSpend, 'gross_profit' => $grossProfit, /* * Real net profit is not available because * there is no Expenses table in the current schema. */ 'net_profit' => null, 'employees' => $employees, 'selling_employees' => $sellingEmployees, 'customers' => $customers, 'payments_collected' => $paymentsCollected + $downPayments,];
        } /* |-------------------------------------------------------------------------- | Gross Profit |-------------------------------------------------------------------------- */
        private function grossProfit(Carbon $from, Carbon $to): float
        {
            $sales = DB::table('order_items')->join('orders', 'orders.id', '=', 'order_items.order_id')->whereBetween('orders.created_at', [$from, $to])->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as quantity'), DB::raw('SUM(order_items.total) as revenue'))->groupBy('order_items.product_id')->get();
            if ($sales->isEmpty()) {
                return 0;
            } /* * Weighted average purchase cost * from all purchases up to report end date. */
            $costs = DB::table('purchase_items')->join('purchases', 'purchases.id', '=', 'purchase_items.purchase_id')->whereDate('purchases.created_at', '<=', $to->toDateString())->select('purchase_items.product_id', DB::raw('SUM(purchase_items.quantity) as quantity'), DB::raw('SUM(purchase_items.total_price) as total_cost'))->groupBy('purchase_items.product_id')->get()->keyBy('product_id');
            $grossProfit = 0;
            foreach ($sales as $sale) {
                $cost = $costs->get($sale->product_id);
                $averageCost = $cost && $cost->quantity > 0 ? (float) $cost->total_cost / (float) $cost->quantity : 0;
                $cogs = (float) $sale->quantity * $averageCost;
                $grossProfit += (float) $sale->revenue - $cogs;
            }
            return round($grossProfit, 2);
        } /* |-------------------------------------------------------------------------- | Sales By Day |-------------------------------------------------------------------------- */
        private function salesByDay(Carbon $from, Carbon $to): array
        {
            return Order::query()->whereBetween('created_at', [$from, $to])->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')->groupByRaw('DATE(created_at)')->orderBy('date')->get()->map(fn($row) => ['date' => $row->date, 'orders' => (int) $row->orders, 'revenue' => (float) $row->revenue,])->toArray();
        } /* |-------------------------------------------------------------------------- | Sales By Employee |-------------------------------------------------------------------------- */
        private function salesByEmployee(Carbon $from, Carbon $to): array
        {
            return Employee::query()->whereHas('orders', function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })->withCount(['orders as sales_count' => function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            },])->withSum(['orders as sales_revenue' => function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            },], 'total')->get()->map(fn($employee) => ['name' => $employee->full_name, 'sales_count' => (int) $employee->sales_count, 'sales_revenue' => (float) $employee->sales_revenue,])->sortByDesc('sales_revenue')->values()->toArray();
        } /* |-------------------------------------------------------------------------- | Top Products |-------------------------------------------------------------------------- */
        private function topProducts(Carbon $from, Carbon $to): array
        {
            return Product::query()->whereHas('orderItems.order', function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })->withSum(['orderItems as sold_quantity' => function ($query) use ($from, $to) {
                $query->whereHas('order', function ($orderQuery) use ($from, $to) {
                    $orderQuery->whereBetween('created_at', [$from, $to]);
                });
            },], 'quantity')->withSum(['orderItems as sales_revenue' => function ($query) use ($from, $to) {
                $query->whereHas('order', function ($orderQuery) use ($from, $to) {
                    $orderQuery->whereBetween('created_at', [$from, $to]);
                });
            },], 'total')->get()->map(fn($product) => ['name' => $product->name, 'sold_quantity' => (int) $product->sold_quantity, 'sales_revenue' => (float) $product->sales_revenue,])->sortByDesc('sold_quantity')->values()->take(10)->toArray();
        } /* |-------------------------------------------------------------------------- | Purchases By Supplier |-------------------------------------------------------------------------- */
        private function purchasesBySupplier(Carbon $from, Carbon $to): array
        {
            return Supplier::query()->whereHas('purchases', function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })->withCount(['purchases as purchases_count' => function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            },])->withSum(['purchases as purchase_total' => function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            },], 'total_price')->get()->map(fn($supplier) => ['name' => $supplier->name, 'purchases_count' => (int) $supplier->purchases_count, 'purchase_total' => (float) $supplier->purchase_total,])->sortByDesc('purchase_total')->values()->take(10)->toArray();
        } /* |-------------------------------------------------------------------------- | Purchases By Day |-------------------------------------------------------------------------- */
        private function purchasesByDay(Carbon $from, Carbon $to): array
        {
            return Purchase::query()->whereBetween('created_at', [$from, $to])->selectRaw('DATE(created_at) as date, COUNT(*) as purchases, SUM(total_price) as total')->groupByRaw('DATE(created_at)')->orderBy('date')->get()->map(fn($row) => ['date' => $row->date, 'purchases' => (int) $row->purchases, 'total' => (float) $row->total,])->toArray();
        } /* |-------------------------------------------------------------------------- | Payment Methods |-------------------------------------------------------------------------- */
        private function paymentMethods(Carbon $from, Carbon $to): array
        {
            return Payment::query()->whereBetween('payment_date', [$from->toDateString(), $to->toDateString(),])->selectRaw('method, SUM(amount) as total')->groupBy('method')->orderByDesc('total')->get()->map(fn($row) => ['method' => $row->method, 'total' => (float) $row->total,])->toArray();
        } /* |-------------------------------------------------------------------------- | Installments |-------------------------------------------------------------------------- */
        private function installmentReport(Carbon $from, Carbon $to): array
        {
            $installments = Installment::query()->whereBetween('start_date', [$from->toDateString(), $to->toDateString(),])->get();
            $paid = (float) Payment::query()->whereBetween('payment_date', [$from->toDateString(), $to->toDateString(),])->sum('amount');
            return ['count' => $installments->count(), 'total' => (float) $installments->sum('total_with_interest'), 'down_payment' => (float) $installments->sum('down_payment'), 'remaining' => (float) Installment::sum('remaining_amount'), 'payments' => $paid,];
        } /* |-------------------------------------------------------------------------- | Customers By Governorate |-------------------------------------------------------------------------- */
        private function customersByGovernorate(Carbon $from, Carbon $to): array
        {
            return DB::table('orders')->join('users', 'users.id', '=', 'orders.user_id')->leftJoin('governorates', 'governorates.id', '=', 'users.governorate_id')->whereBetween('orders.created_at', [$from, $to])->selectRaw("COALESCE(governorates.name, 'Unknown') as name, COUNT(DISTINCT users.id) as customers, SUM(orders.total) as revenue")->groupBy('governorates.id', 'governorates.name')->orderByDesc('revenue')->get()->map(fn($row) => ['name' => $row->name, 'customers' => (int) $row->customers, 'revenue' => (float) $row->revenue,])->take(10)->values()->toArray();
        } /* |-------------------------------------------------------------------------- | Stock |-------------------------------------------------------------------------- */
        private function stockReport(): array
        {
            $products = Product::query()->get();
            $totalQuantity = (int) $products->sum('current_quantity');
            $retailValue = (float) $products->sum(fn($product) => (float) $product->current_quantity * (float) $product->price);
            $lowStock = $products->where('current_quantity', '<=', 5)->count();
            $outOfStock = $products->where('current_quantity', '<=', 0)->count();
            return ['products' => $products->count(), 'total_quantity' => $totalQuantity, 'retail_value' => round($retailValue, 2), 'low_stock' => $lowStock, 'out_of_stock' => $outOfStock,];
        }
    }
