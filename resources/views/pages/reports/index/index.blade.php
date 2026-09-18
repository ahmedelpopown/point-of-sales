<div class="px-4 pb-10 sm:px-6 lg:px-8">

    {{-- ========================================================= --}}
    {{-- Header --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Reports Dashboard
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Complete overview of sales, purchases, products,
                employees, customers and installments.
            </p>
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Date Filters --}}
    {{-- ========================================================= --}}

    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    From
                </label>

                <input
                    wire:model.live="fromDate"
                    type="date"
                    class="mt-1 block w-full rounded-lg border-gray-300"
                >
            </div>


            <div>
                <label class="block text-sm font-medium text-gray-700">
                    To
                </label>

                <input
                    wire:model.live="toDate"
                    type="date"
                    class="mt-1 block w-full rounded-lg border-gray-300"
                >
            </div>


            <div class="flex items-end">

                <button
                    type="button"
                    wire:click="refreshReports"
                    class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
                >
                    Refresh Reports
                </button>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Main KPI Cards --}}
    {{-- ========================================================= --}}

    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Sales --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Sales Operations
            </p>

            <p class="mt-2 text-3xl font-bold text-gray-900">
                {{ number_format($this->report['summary']['sales_count']) }}
            </p>

            <p class="mt-2 text-sm text-gray-500">
                Revenue:

                <span class="font-semibold text-gray-900">
                    {{ number_format($this->report['summary']['sales_revenue'], 2) }}
                </span>
            </p>

        </div>


        {{-- Purchases --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Purchase Operations
            </p>

            <p class="mt-2 text-3xl font-bold text-gray-900">
                {{ number_format($this->report['summary']['purchase_count']) }}
            </p>

            <p class="mt-2 text-sm text-gray-500">
                Cost:

                <span class="font-semibold text-gray-900">
                    {{ number_format($this->report['summary']['purchase_spend'], 2) }}
                </span>
            </p>

        </div>


        {{-- Gross Profit --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Gross Profit
            </p>

            <p class="mt-2 text-3xl font-bold text-green-600">
                {{ number_format($this->report['summary']['gross_profit'], 2) }}
            </p>

            <p class="mt-2 text-xs text-gray-500">
                Estimated using purchase cost.
            </p>

        </div>


        {{-- Net Profit --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Net Profit
            </p>

            <p class="mt-2 text-xl font-bold text-gray-700">
                Not available
            </p>

            <p class="mt-2 text-xs text-gray-500">
                Add an Expenses module for true net profit.
            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- People + Stock --}}
    {{-- ========================================================= --}}

    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">

        <div class="rounded-lg bg-white p-5 shadow">

            <p class="text-sm text-gray-500">
                Employees
            </p>

            <p class="mt-2 text-2xl font-bold text-gray-900">
                {{ number_format($this->report['summary']['employees']) }}
            </p>

        </div>


        <div class="rounded-lg bg-white p-5 shadow">

            <p class="text-sm text-gray-500">
                Selling Employees
            </p>

            <p class="mt-2 text-2xl font-bold text-indigo-600">
                {{ number_format($this->report['summary']['selling_employees']) }}
            </p>

        </div>


        <div class="rounded-lg bg-white p-5 shadow">

            <p class="text-sm text-gray-500">
                Customers
            </p>

            <p class="mt-2 text-2xl font-bold text-gray-900">
                {{ number_format($this->report['summary']['customers']) }}
            </p>

        </div>


        <div class="rounded-lg bg-white p-5 shadow">

            <p class="text-sm text-gray-500">
                Stock Units
            </p>

            <p class="mt-2 text-2xl font-bold text-gray-900">
                {{ number_format($this->report['stock']['total_quantity']) }}
            </p>

        </div>


        <div class="rounded-lg bg-white p-5 shadow">

            <p class="text-sm text-gray-500">
                Low Stock
            </p>

            <p class="mt-2 text-2xl font-bold text-red-600">
                {{ number_format($this->report['stock']['low_stock']) }}
            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- More KPIs --}}
    {{-- ========================================================= --}}

    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">

        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Payments Collected
            </p>

            <p class="mt-2 text-2xl font-bold text-green-600">
                {{ number_format($this->report['summary']['payments_collected'], 2) }}
            </p>

        </div>


        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Stock Retail Value
            </p>

            <p class="mt-2 text-2xl font-bold text-gray-900">
                {{ number_format($this->report['stock']['retail_value'], 2) }}
            </p>

        </div>


        <div class="rounded-lg bg-white p-6 shadow">

            <p class="text-sm text-gray-500">
                Out of Stock
            </p>

            <p class="mt-2 text-2xl font-bold text-red-600">
                {{ number_format($this->report['stock']['out_of_stock']) }}
            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Sales Charts --}}
    {{-- ========================================================= --}}

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Sales Revenue --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <h2 class="text-lg font-semibold text-gray-900">
                Sales Revenue
            </h2>

            <div
                wire:ignore
                class="relative mt-6 h-80"
            >
                <canvas id="salesRevenueChart"></canvas>
            </div>

        </div>


        {{-- Sales Operations --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <h2 class="text-lg font-semibold text-gray-900">
                Sales Operations
            </h2>

            <div
                wire:ignore
                class="relative mt-6 h-80"
            >
                <canvas id="salesCountChart"></canvas>
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Employees + Products --}}
    {{-- ========================================================= --}}

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Employee Sales --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <h2 class="text-lg font-semibold text-gray-900">
                Employee Sales
            </h2>

            <div
                wire:ignore
                class="relative mt-6 h-80"
            >
                <canvas id="employeeSalesChart"></canvas>
            </div>

        </div>


        {{-- Top Products --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <h2 class="text-lg font-semibold text-gray-900">
                Top Products
            </h2>

            <div
                wire:ignore
                class="relative mt-6 h-80"
            >
                <canvas id="topProductsChart"></canvas>
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Purchases --}}
    {{-- ========================================================= --}}

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Purchases By Supplier --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <h2 class="text-lg font-semibold text-gray-900">
                Purchases By Supplier
            </h2>

            <div
                wire:ignore
                class="relative mt-6 h-80"
            >
                <canvas id="supplierPurchaseChart"></canvas>
            </div>

        </div>


        {{-- Payment Methods --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <h2 class="text-lg font-semibold text-gray-900">
                Payment Methods
            </h2>

            <div
                wire:ignore
                class="relative mt-6 h-80"
            >
                <canvas id="paymentMethodsChart"></canvas>
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Customer Geography --}}
    {{-- ========================================================= --}}

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Customers By Governorate --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <h2 class="text-lg font-semibold text-gray-900">
                Customers By Governorate
            </h2>

            <div
                wire:ignore
                class="relative mt-6 h-80"
            >
                <canvas id="customersGovernorateChart"></canvas>
            </div>

        </div>


        {{-- Installment Overview --}}
        <div class="rounded-lg bg-white p-6 shadow">

            <h2 class="text-lg font-semibold text-gray-900">
                Installment Overview
            </h2>

            <div
                wire:ignore
                class="relative mt-6 h-80"
            >
                <canvas id="installmentChart"></canvas>
            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Employee Performance Table --}}
    {{-- ========================================================= --}}

    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <div class="mb-5">

            <h2 class="text-lg font-semibold text-gray-900">
                Employee Performance
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Who sold what and how much.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-300">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-700">
                            Employee
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-700">
                            Sales
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-700">
                            Revenue
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200">

                    @forelse($this->report['sales_by_employee'] as $employee)

                        <tr class="hover:bg-gray-50">

                            <td class="px-3 py-4 text-sm font-medium text-gray-900">
                                {{ $employee['name'] }}
                            </td>

                            <td class="px-3 py-4 text-sm text-gray-700">
                                {{ number_format($employee['sales_count']) }}
                            </td>

                            <td class="px-3 py-4 text-sm font-semibold text-gray-900">
                                {{ number_format($employee['sales_revenue'], 2) }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="3"
                                class="px-3 py-8 text-center text-sm text-gray-500"
                            >
                                No sales found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Top Selling Products Table --}}
    {{-- ========================================================= --}}

    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <div class="mb-5">

            <h2 class="text-lg font-semibold text-gray-900">
                Top Selling Products
            </h2>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-300">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-700">
                            Product
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-700">
                            Quantity Sold
                        </th>

                        <th class="px-3 py-3 text-left text-sm font-semibold text-gray-700">
                            Revenue
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200">

                    @forelse($this->report['top_products'] as $product)

                        <tr class="hover:bg-gray-50">

                            <td class="px-3 py-4 text-sm font-medium text-gray-900">
                                {{ $product['name'] }}
                            </td>

                            <td class="px-3 py-4 text-sm text-gray-700">
                                {{ number_format($product['sold_quantity']) }}
                            </td>

                            <td class="px-3 py-4 text-sm font-semibold text-gray-900">
                                {{ number_format($product['sales_revenue'], 2) }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="3"
                                class="px-3 py-8 text-center text-sm text-gray-500"
                            >
                                No product sales found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Installment Details --}}
    {{-- ========================================================= --}}

    <div class="mt-8 rounded-lg bg-white p-6 shadow">

        <h2 class="text-lg font-semibold text-gray-900">
            Installment Report
        </h2>


        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-5">

            <div>

                <p class="text-sm text-gray-500">
                    Installments
                </p>

                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ number_format($this->report['installments']['count']) }}
                </p>

            </div>


            <div>

                <p class="text-sm text-gray-500">
                    Total
                </p>

                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ number_format($this->report['installments']['total'], 2) }}
                </p>

            </div>


            <div>

                <p class="text-sm text-gray-500">
                    Down Payments
                </p>

                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ number_format($this->report['installments']['down_payment'], 2) }}
                </p>

            </div>


            <div>

                <p class="text-sm text-gray-500">
                    Payments
                </p>

                <p class="mt-1 text-2xl font-bold text-green-600">
                    {{ number_format($this->report['installments']['payments'], 2) }}
                </p>

            </div>


            <div>

                <p class="text-sm text-gray-500">
                    Outstanding
                </p>

                <p class="mt-1 text-2xl font-bold text-red-600">
                    {{ number_format($this->report['installments']['remaining'], 2) }}
                </p>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- Report Data For JavaScript --}}
    {{-- ========================================================= --}}

    <script
        type="application/json"
        id="reports-data"
    >{!! json_encode($this->chartData) !!}</script>


    {{-- ========================================================= --}}
    {{-- Chart.js --}}
    {{-- ========================================================= --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>

        let reportsCharts = {};

        let reportData = {};


        /*
        |--------------------------------------------------------------------------
        | Colors
        |--------------------------------------------------------------------------
        */

        const chartColors = {

            blue: '#3B82F6',

            blueLight: 'rgba(59, 130, 246, 0.15)',

            green: '#10B981',

            orange: '#F59E0B',

            red: '#EF4444',

            purple: '#8B5CF6',

            cyan: '#06B6D4',

            pink: '#EC4899',

            yellow: '#EAB308',

        };


        const palette = [

            chartColors.blue,

            chartColors.green,

            chartColors.orange,

            chartColors.red,

            chartColors.purple,

            chartColors.cyan,

            chartColors.pink,

            chartColors.yellow,

        ];


        /*
        |--------------------------------------------------------------------------
        | Load Report Data
        |--------------------------------------------------------------------------
        */

        function loadReportData() {

            const element =
                document.getElementById('reports-data');


            if (!element) {
                return false;
            }


            try {

                reportData =
                    JSON.parse(element.textContent);

                return true;

            } catch (error) {

                console.error(
                    'Failed to parse reports data:',
                    error
                );

                return false;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Destroy Charts
        |--------------------------------------------------------------------------
        */

        function destroyReportsCharts() {

            Object.values(reportsCharts).forEach(
                chart => {

                    if (chart) {
                        chart.destroy();
                    }

                }
            );


            reportsCharts = {};

        }


        /*
        |--------------------------------------------------------------------------
        | Generate Colors
        |--------------------------------------------------------------------------
        */

        function getPalette(count) {

            return Array.from(
                { length: count },

                (_, index) =>
                    palette[index % palette.length]
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Create Reports Charts
        |--------------------------------------------------------------------------
        */

        function createReportsCharts() {

            if (!loadReportData()) {
                return;
            }


            destroyReportsCharts();


            /*
            |--------------------------------------------------------------------------
            | Sales Revenue
            |--------------------------------------------------------------------------
            */

            const salesRevenueCanvas =
                document.getElementById(
                    'salesRevenueChart'
                );


            if (salesRevenueCanvas) {

                reportsCharts.salesRevenue =
                    new Chart(

                        salesRevenueCanvas,

                        {

                            type: 'line',


                            data: {

                                labels:
                                    reportData.sales_revenue?.labels ?? [],


                                datasets: [

                                    {

                                        label:
                                            'Sales Revenue',


                                        data:
                                            reportData.sales_revenue?.data ?? [],


                                        borderColor:
                                            chartColors.blue,


                                        backgroundColor:
                                            chartColors.blueLight,


                                        borderWidth: 3,


                                        pointBackgroundColor:
                                            chartColors.blue,


                                        pointBorderColor:
                                            '#ffffff',


                                        pointBorderWidth: 2,


                                        pointRadius: 4,


                                        pointHoverRadius: 6,


                                        fill: true,


                                        tension: 0.35,

                                    }

                                ]

                            },


                            options: {

                                responsive: true,

                                maintainAspectRatio: false,


                                plugins: {

                                    legend: {
                                        display: true
                                    }

                                },


                                scales: {

                                    y: {
                                        beginAtZero: true
                                    }

                                }

                            }

                        }

                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Sales Operations
            |--------------------------------------------------------------------------
            */

            const salesCountCanvas =
                document.getElementById(
                    'salesCountChart'
                );


            if (salesCountCanvas) {

                reportsCharts.salesCount =
                    new Chart(

                        salesCountCanvas,

                        {

                            type: 'bar',


                            data: {

                                labels:
                                    reportData.sales_operations?.labels ?? [],


                                datasets: [

                                    {

                                        label:
                                            'Sales Operations',


                                        data:
                                            reportData.sales_operations?.data ?? [],


                                        backgroundColor:
                                            chartColors.green,


                                        borderColor:
                                            chartColors.green,


                                        borderWidth: 1,


                                        borderRadius: 6,

                                    }

                                ]

                            },


                            options: {

                                responsive: true,

                                maintainAspectRatio: false,


                                plugins: {

                                    legend: {
                                        display: true
                                    }

                                },


                                scales: {

                                    y: {

                                        beginAtZero: true,


                                        ticks: {
                                            precision: 0
                                        }

                                    }

                                }

                            }

                        }

                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Employee Sales
            |--------------------------------------------------------------------------
            */

            const employeeSalesCanvas =
                document.getElementById(
                    'employeeSalesChart'
                );


            if (employeeSalesCanvas) {

                reportsCharts.employeeSales =
                    new Chart(

                        employeeSalesCanvas,

                        {

                            type: 'bar',


                            data: {

                                labels:
                                    reportData.employee_sales?.labels ?? [],


                                datasets: [

                                    {

                                        label:
                                            'Employee Sales',


                                        data:
                                            reportData.employee_sales?.data ?? [],


                                        backgroundColor:
                                            chartColors.purple,


                                        borderColor:
                                            chartColors.purple,


                                        borderWidth: 1,


                                        borderRadius: 6,

                                    }

                                ]

                            },


                            options: {

                                indexAxis: 'y',

                                responsive: true,

                                maintainAspectRatio: false,


                                plugins: {

                                    legend: {
                                        display: true
                                    }

                                },


                                scales: {

                                    x: {
                                        beginAtZero: true
                                    }

                                }

                            }

                        }

                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Top Products
            |--------------------------------------------------------------------------
            */

            const topProductsCanvas =
                document.getElementById(
                    'topProductsChart'
                );


            if (topProductsCanvas) {

                const labels =
                    reportData.top_products?.labels ?? [];


                const data =
                    reportData.top_products?.data ?? [];


                reportsCharts.topProducts =
                    new Chart(

                        topProductsCanvas,

                        {

                            type: 'bar',


                            data: {

                                labels: labels,


                                datasets: [

                                    {

                                        label:
                                            'Products Sold',


                                        data: data,


                                        backgroundColor:
                                            getPalette(labels.length),


                                        borderWidth: 1,


                                        borderRadius: 6,

                                    }

                                ]

                            },


                            options: {

                                responsive: true,

                                maintainAspectRatio: false,


                                plugins: {

                                    legend: {
                                        display: true
                                    }

                                },


                                scales: {

                                    y: {

                                        beginAtZero: true,


                                        ticks: {
                                            precision: 0
                                        }

                                    }

                                }

                            }

                        }

                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Purchases By Supplier
            |--------------------------------------------------------------------------
            */

            const supplierPurchaseCanvas =
                document.getElementById(
                    'supplierPurchaseChart'
                );


            if (supplierPurchaseCanvas) {

                const labels =
                    reportData.supplier_purchases?.labels ?? [];


                const data =
                    reportData.supplier_purchases?.data ?? [];


                reportsCharts.supplierPurchase =
                    new Chart(

                        supplierPurchaseCanvas,

                        {

                            type: 'doughnut',


                            data: {

                                labels: labels,


                                datasets: [

                                    {

                                        data: data,


                                        backgroundColor:
                                            getPalette(labels.length),


                                        borderColor:
                                            '#ffffff',


                                        borderWidth: 2,


                                        hoverOffset: 8,

                                    }

                                ]

                            },


                            options: {

                                responsive: true,

                                maintainAspectRatio: false,


                                cutout: '60%',


                                plugins: {

                                    legend: {

                                        position: 'bottom'

                                    }

                                }

                            }

                        }

                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Payment Methods
            |--------------------------------------------------------------------------
            */

            const paymentMethodsCanvas =
                document.getElementById(
                    'paymentMethodsChart'
                );


            if (paymentMethodsCanvas) {

                const labels =
                    reportData.payment_methods?.labels ?? [];


                const data =
                    reportData.payment_methods?.data ?? [];


                reportsCharts.paymentMethods =
                    new Chart(

                        paymentMethodsCanvas,

                        {

                            type: 'doughnut',


                            data: {

                                labels: labels,


                                datasets: [

                                    {

                                        data: data,


                                        backgroundColor: [

                                            chartColors.green,

                                            chartColors.blue,

                                            chartColors.orange,

                                            chartColors.purple,

                                            chartColors.cyan,

                                        ],


                                        borderColor:
                                            '#ffffff',


                                        borderWidth: 2,


                                        hoverOffset: 8,

                                    }

                                ]

                            },


                            options: {

                                responsive: true,

                                maintainAspectRatio: false,


                                cutout: '60%',


                                plugins: {

                                    legend: {

                                        position: 'bottom'

                                    }

                                }

                            }

                        }

                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Customers By Governorate
            |--------------------------------------------------------------------------
            */

            const governorateCanvas =
                document.getElementById(
                    'customersGovernorateChart'
                );


            if (governorateCanvas) {

                const labels =
                    reportData.customers_by_governorate?.labels ?? [];


                const data =
                    reportData.customers_by_governorate?.data ?? [];


                reportsCharts.governorates =
                    new Chart(

                        governorateCanvas,

                        {

                            type: 'bar',


                            data: {

                                labels: labels,


                                datasets: [

                                    {

                                        label:
                                            'Customers',


                                        data: data,


                                        backgroundColor:
                                            chartColors.cyan,


                                        borderColor:
                                            chartColors.cyan,


                                        borderWidth: 1,


                                        borderRadius: 6,

                                    }

                                ]

                            },


                            options: {

                                responsive: true,

                                maintainAspectRatio: false,


                                plugins: {

                                    legend: {

                                        display: true

                                    }

                                },


                                scales: {

                                    y: {

                                        beginAtZero: true,


                                        ticks: {

                                            precision: 0

                                        }

                                    }

                                }

                            }

                        }

                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Installment Overview
            |--------------------------------------------------------------------------
            */

            const installmentCanvas =
                document.getElementById(
                    'installmentChart'
                );


            if (installmentCanvas) {

                reportsCharts.installments =
                    new Chart(

                        installmentCanvas,

                        {

                            type: 'doughnut',


                            data: {

                                labels: [

                                    'Total Amount',

                                    'Paid Amount',

                                    'Remaining Amount',

                                ],


                                datasets: [

                                    {

                                        data: [

                                            reportData.installments?.total
                                                ?? 0,


                                            reportData.installments?.paid
                                                ?? 0,


                                            reportData.installments?.remaining
                                                ?? 0,

                                        ],


                                        backgroundColor: [

                                            chartColors.blue,

                                            chartColors.green,

                                            chartColors.red,

                                        ],


                                        borderColor:
                                            '#ffffff',


                                        borderWidth: 2,


                                        hoverOffset: 8,

                                    }

                                ]

                            },


                            options: {

                                responsive: true,

                                maintainAspectRatio: false,


                                cutout: '60%',


                                plugins: {

                                    legend: {

                                        position: 'bottom'

                                    }

                                }

                            }

                        }

                    );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Initial Load
        |--------------------------------------------------------------------------
        */

        function initializeReportsCharts() {

            if (
                typeof Chart === 'undefined'
            ) {

                setTimeout(
                    initializeReportsCharts,
                    100
                );

                return;

            }


            createReportsCharts();

        }


        initializeReportsCharts();


        /*
        |--------------------------------------------------------------------------
        | Livewire Update
        |--------------------------------------------------------------------------
        */

        if (
            typeof Livewire !== 'undefined'
        ) {

            Livewire.on(
                'reports-updated',
                () => {

                    setTimeout(
                        () => {
                            createReportsCharts();
                        },
                        150
                    );

                }
            );

        }

    </script>

</div>