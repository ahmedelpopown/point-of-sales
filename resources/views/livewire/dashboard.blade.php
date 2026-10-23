<div class="pos-dash">
    @php
        $report = $this->report;
        $summary = $report['summary'] ?? [];
        $stock = $report['stock'] ?? [];
        $installments = $report['installments'] ?? [];
        $dailySales = $this->paginatedRows('dailySales');
        $topProducts = $this->paginatedRows('topProducts');
        $lowProducts = $this->paginatedRows('lowProducts');
        $payments = $this->paginatedRows('payments');
        $openInstallments = $this->paginatedRows('installments');
        $employees = $this->paginatedRows('employees');
        $suppliers = $this->paginatedRows('suppliers');
    @endphp

    <style>
        .pos-dash {
            --pd-bg: #f5f7fb;
            --pd-surface: #ffffff;
            --pd-ink: #182230;
            --pd-muted: #778294;
            --pd-border: #e7ebf1;
            --pd-soft: #f8f9fc;
            --pd-primary: #3f64e8;
            --pd-primary-soft: #edf1ff;
            --pd-positive: #159782;
            --pd-positive-soft: #e8f7f2;
            --pd-warning: #c88b28;
            --pd-warning-soft: #fff5e3;
            --pd-danger: #d45c65;
            --pd-danger-soft: #fff0f1;
            color: var(--pd-ink);
            background: var(--pd-bg);
            min-height: 100%;
            padding: clamp(16px, 2.2vw, 30px);
            font-size: 13px;
        }
        .pos-dash * { box-sizing: border-box; }
        .pd-shell { max-width: 1540px; margin: 0 auto; }
        .pd-header { display:flex; align-items:flex-end; justify-content:space-between; gap:24px; margin: 0 0 22px; }
        .pd-eyebrow { display:flex; align-items:center; gap:8px; margin:0 0 8px; color:var(--pd-primary); font-size:10px; font-weight:800; letter-spacing:.16em; text-transform:uppercase; }
        .pd-eyebrow-mark { display:inline-block; width:7px; height:7px; border-radius:2px; background:var(--pd-primary); }
        .pd-title { margin:0; color:var(--pd-ink); font-size:clamp(25px, 2vw, 31px); line-height:1.15; font-weight:780; letter-spacing:-.045em; }
        .pd-subtitle { margin:8px 0 0; color:var(--pd-muted); font-size:12px; }
        .pd-filter { display:flex; align-items:flex-end; flex-wrap:wrap; gap:10px; padding:10px; background:var(--pd-surface); border:1px solid var(--pd-border); border-radius:13px; }
        .pd-filter-field { display:flex; flex-direction:column; gap:5px; }
        .pd-filter label { font-size:10px; font-weight:750; color:var(--pd-muted); }
        .pd-filter input { width:150px; min-height:35px; padding:7px 10px; border:1px solid var(--pd-border); border-radius:8px; outline:none; background:#fff; color:var(--pd-ink); font-size:12px; }
        .pd-filter input:focus { border-color:var(--pd-primary); box-shadow:0 0 0 3px rgba(63,100,232,.10); }
        .pd-button { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:35px; padding:0 15px; border:1px solid var(--pd-primary); border-radius:8px; background:var(--pd-primary); color:#fff; font-size:12px; font-weight:750; cursor:pointer; transition:background .16s ease, transform .16s ease; }
        .pd-button:hover { background:#3154d2; }
        .pd-button:active { transform:translateY(1px); }
        .pd-button:disabled { opacity:.6; cursor:wait; }
        .pd-flash { margin:0 0 16px; padding:11px 14px; border:1px solid #bde9da; border-radius:10px; color:#126b59; background:var(--pd-positive-soft); }
        .pd-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); background:var(--pd-surface); border:1px solid var(--pd-border); border-radius:15px; overflow:hidden; margin-bottom:18px; }
        .pd-metric { position:relative; min-width:0; padding:19px 21px 16px; }
        .pd-metric + .pd-metric { border-left:1px solid var(--pd-border); }
        .pd-metric-label { display:flex; align-items:center; gap:8px; color:var(--pd-muted); font-size:11px; font-weight:650; }
        .pd-metric-dot { width:8px; height:8px; border-radius:3px; flex:none; background:var(--pd-primary); }
        .pd-metric-dot.positive { background:var(--pd-positive); }
        .pd-metric-dot.warning { background:var(--pd-warning); }
        .pd-metric-value { margin:12px 0 6px; color:var(--pd-ink); font-size:clamp(21px, 1.9vw, 28px); font-weight:780; letter-spacing:-.045em; line-height:1.05; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .pd-metric-note { color:#9aa3b1; font-size:10px; }
        .pd-quickline { display:flex; flex-wrap:wrap; gap:0; margin:-18px 0 18px; padding:12px 17px; background:#eef1f7; border-radius:0 0 13px 13px; border:1px solid var(--pd-border); border-top:0; }
        .pd-quickitem { display:flex; align-items:baseline; gap:7px; padding:0 18px; color:var(--pd-muted); font-size:11px; }
        .pd-quickitem:first-child { padding-left:3px; }
        .pd-quickitem + .pd-quickitem { border-left:1px solid #dce2eb; }
        .pd-quickitem strong { color:var(--pd-ink); font-size:12px; font-weight:750; font-variant-numeric:tabular-nums; }
        .pd-quickitem .warn { color:var(--pd-warning); }
        .pd-quickitem .danger { color:var(--pd-danger); }
        .pd-panel { overflow:hidden; margin-top:18px; border:1px solid var(--pd-border); border-radius:15px; background:var(--pd-surface); box-shadow:0 2px 9px rgba(25,39,64,.025); }
        .pd-panel-head { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:18px 21px; border-bottom:1px solid var(--pd-border); }
        .pd-heading-wrap { min-width:0; }
        .pd-section-title { margin:0; color:var(--pd-ink); font-size:15px; line-height:1.3; font-weight:780; letter-spacing:-.02em; }
        .pd-section-note { margin:5px 0 0; color:var(--pd-muted); font-size:11px; line-height:1.5; }
        .pd-period-tag { flex:none; padding:6px 9px; border-radius:7px; color:var(--pd-primary); background:var(--pd-primary-soft); font-size:10px; font-weight:750; }
        .pd-split { display:grid; grid-template-columns:minmax(0,1.25fr) minmax(380px,1fr); }
        .pd-split-equal { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); }
        .pd-pane { min-width:0; padding:19px 21px; }
        .pd-pane + .pd-pane { border-left:1px solid var(--pd-border); }
        .pd-pane-title { display:flex; justify-content:space-between; align-items:center; gap:12px; margin:0 0 4px; color:#344054; font-size:12px; font-weight:780; }
        .pd-pane-note { margin:0 0 13px; color:#9aa3b1; font-size:10px; }
        .pd-chart { position:relative; width:100%; height:270px; }
        .pd-chart-short { height:205px; }
        .pd-product-layout { display:grid; grid-template-columns:minmax(270px,.78fr) minmax(0,1.22fr); }
        .pd-product-chart { padding:19px 20px; border-right:1px solid var(--pd-border); }
        .pd-product-best { padding:19px 20px; min-width:0; }
        .pd-bottom-pane { padding:17px 20px 19px; border-top:1px solid var(--pd-border); }
        .pd-product-bottom-grid { display:grid; grid-template-columns:minmax(0,1fr); }
        .pd-table-heading { display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin:0 0 10px; }
        .pd-table-title { margin:0; color:#344054; font-size:12px; font-weight:780; }
        .pd-table-note { margin:4px 0 0; color:#9aa3b1; font-size:10px; }
        .pd-tag { display:inline-flex; align-items:center; gap:5px; flex:none; border-radius:6px; padding:5px 8px; font-size:9px; font-weight:800; letter-spacing:.025em; }
        .pd-tag-primary { color:var(--pd-primary); background:var(--pd-primary-soft); }
        .pd-tag-positive { color:var(--pd-positive); background:var(--pd-positive-soft); }
        .pd-tag-warning { color:var(--pd-warning); background:var(--pd-warning-soft); }
        .pd-tag-danger { color:var(--pd-danger); background:var(--pd-danger-soft); }
        .pd-table-wrap { width:100%; overflow-x:auto; }
        .pd-table { width:100%; border-collapse:separate; border-spacing:0; text-align:left; font-size:11px; }
        .pd-table thead th { padding:10px 10px; color:#8993a3; background:#f8f9fc; border-top:1px solid #edf0f5; border-bottom:1px solid #e9edf3; font-size:9px; font-weight:800; letter-spacing:.075em; text-transform:uppercase; white-space:nowrap; }
        .pd-table thead th:first-child { padding-left:12px; border-top-left-radius:7px; }
        .pd-table thead th:last-child { padding-right:12px; border-top-right-radius:7px; }
        .pd-table tbody td { padding:11px 10px; border-bottom:1px solid #f0f2f6; color:#687386; vertical-align:middle; }
        .pd-table tbody tr:last-child td { border-bottom:0; }
        .pd-table tbody tr { transition:background .12s ease; }
        .pd-table tbody tr:hover { background:#fafbfe; }
        .pd-table tbody td:first-child { padding-left:12px; }
        .pd-table tbody td:last-child { padding-right:12px; }
        .pd-table .pd-strong { color:#273244; font-weight:700; }
        .pd-table .pd-number { color:#273244; text-align:right; white-space:nowrap; font-variant-numeric:tabular-nums; }
        .pd-table .pd-number-muted { text-align:right; white-space:nowrap; font-variant-numeric:tabular-nums; }
        .pd-table .pd-no-data { height:90px; color:#9ba4b3; text-align:center; font-size:11px; }
        .pd-rank { display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; margin-right:8px; border-radius:7px; color:#61708a; background:#f0f3f8; font-size:10px; font-weight:800; }
        .pd-rank-first { color:var(--pd-primary); background:var(--pd-primary-soft); }
        .pd-payment-area { display:grid; grid-template-columns:minmax(0,1fr) 175px; gap:16px; align-items:center; }
        .pd-method-chart { position:relative; height:175px; }
        .pd-account-summary { display:flex; flex-wrap:wrap; gap:8px; }
        .pd-account-chip { display:inline-flex; align-items:center; gap:6px; padding:7px 9px; border-radius:8px; color:#657084; background:#f7f8fb; font-size:10px; }
        .pd-account-chip strong { color:var(--pd-ink); font-size:11px; }
        .pd-footer-note { padding:14px 3px 0; color:#98a1af; font-size:10px; line-height:1.6; }
        .pd-pagination { display:flex; align-items:center; justify-content:space-between; gap:12px; padding-top:12px; margin-top:9px; border-top:1px solid #eef1f5; }
        .pd-pagination-info { color:#9aa3b1; font-size:10px; }
        .pd-pagination-controls { display:flex; align-items:center; gap:6px; }
        .pd-page-btn { display:inline-flex; align-items:center; justify-content:center; min-height:29px; padding:0 10px; border:1px solid var(--pd-border); border-radius:7px; color:#586477; background:white; font-size:10px; font-weight:700; cursor:pointer; transition:all .15s ease; }
        .pd-page-btn:hover:not(:disabled) { color:var(--pd-primary); border-color:#bdc9ff; background:var(--pd-primary-soft); }
        .pd-page-btn:disabled { opacity:.4; cursor:not-allowed; }
        .pd-page-number { min-width:43px; text-align:center; color:#687386; font-size:10px; font-weight:750; }
        .pd-alert-number { color:var(--pd-danger) !important; font-weight:750; }
        @media (max-width: 1100px) {
            .pd-header { align-items:flex-start; flex-direction:column; }
            .pd-filter { width:100%; }
            .pd-filter-field { flex:1; }
            .pd-filter input { width:100%; }
            .pd-split { grid-template-columns:minmax(0,1fr); }
            .pd-split .pd-pane + .pd-pane { border-left:0; border-top:1px solid var(--pd-border); }
            .pd-product-layout { grid-template-columns:minmax(0,1fr); }
            .pd-product-chart { border-right:0; border-bottom:1px solid var(--pd-border); }
            .pd-product-chart .pd-chart { height:240px; }
        }
        @media (max-width: 760px) {
            .pd-summary { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .pd-metric { padding:15px 14px; }
            .pd-metric:nth-child(3) { border-left:0; border-top:1px solid var(--pd-border); }
            .pd-metric:nth-child(4) { border-top:1px solid var(--pd-border); }
            .pd-metric-value { font-size:22px; }
            .pd-quickline { margin-top:-18px; padding:12px; gap:9px 0; }
            .pd-quickitem { padding:0 10px; }
            .pd-quickitem:nth-child(odd) { padding-left:0; }
            .pd-quickitem:nth-child(3) { border-left:0; }
            .pd-panel-head { align-items:flex-start; flex-direction:column; padding:16px; }
            .pd-pane, .pd-product-chart, .pd-product-best, .pd-bottom-pane { padding:15px; }
            .pd-split-equal { grid-template-columns:minmax(0,1fr); }
            .pd-split-equal .pd-pane + .pd-pane { border-left:0; border-top:1px solid var(--pd-border); }
            .pd-payment-area { grid-template-columns:minmax(0,1fr); }
            .pd-method-chart { height:180px; max-width:250px; margin:0 auto; }
            .pd-filter { align-items:stretch; }
            .pd-filter .pd-button { width:100%; }
            .pd-pagination { align-items:flex-start; flex-direction:column; }
        }
    </style>

    <div class="pd-shell">
        <header class="pd-header">
            <div>
                <p class="pd-eyebrow"><span class="pd-eyebrow-mark"></span> POS / BUSINESS INSIGHTS</p>
                <h1 class="pd-title">Dashboard overview</h1>
                <p class="pd-subtitle">A clear picture of sales, products, collections and stock.</p>
            </div>
            <form wire:submit="refreshDashboard" class="pd-filter">
                <div class="pd-filter-field">
                    <label for="dashboard-from">FROM</label>
                    <input id="dashboard-from" wire:model="fromDate" type="date">
                    @error('fromDate') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>
                <div class="pd-filter-field">
                    <label for="dashboard-to">TO</label>
                    <input id="dashboard-to" wire:model="toDate" type="date">
                    @error('toDate') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>
                <button type="submit" wire:loading.attr="disabled" wire:target="refreshDashboard" class="pd-button">
                    <svg wire:loading.remove wire:target="refreshDashboard" width="14" height="14" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3.5 10a6.5 6.5 0 0 1 11.7-3.9V3.5a.8.8 0 1 1 1.6 0v4.3a.8.8 0 0 1-.8.8h-4.3a.8.8 0 1 1 0-1.6h2.4A4.9 4.9 0 1 0 15 12a.8.8 0 1 1 1.5.6A6.5 6.5 0 0 1 3.5 10Z"/></svg>
                    <svg wire:loading wire:target="refreshDashboard" width="14" height="14" class="animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity=".25"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                    <span wire:loading.remove wire:target="refreshDashboard">Apply filters</span>
                    <span wire:loading wire:target="refreshDashboard">Updating</span>
                </button>
            </form>
        </header>

        @if (session()->has('message'))
            <div class="pd-flash">{{ session('message') }}</div>
        @endif

        <section class="pd-summary" aria-label="Business summary">
            <div class="pd-metric">
                <div class="pd-metric-label"><span class="pd-metric-dot"></span>Sales revenue</div>
                <div class="pd-metric-value">{{ number_format((float) ($summary['sales_revenue'] ?? 0), 2) }}</div>
                <div class="pd-metric-note">{{ number_format((int) ($summary['sales_count'] ?? 0)) }} sales in period</div>
            </div>
            <div class="pd-metric">
                <div class="pd-metric-label"><span class="pd-metric-dot positive"></span>Estimated gross profit</div>
                <div class="pd-metric-value">{{ number_format((float) ($summary['gross_profit'] ?? 0), 2) }}</div>
                <div class="pd-metric-note">Based on available product costs</div>
            </div>
            <div class="pd-metric">
                <div class="pd-metric-label"><span class="pd-metric-dot warning"></span>Purchase spending</div>
                <div class="pd-metric-value">{{ number_format((float) ($summary['purchase_spend'] ?? 0), 2) }}</div>
                <div class="pd-metric-note">{{ number_format((int) ($summary['purchase_count'] ?? 0)) }} purchases in period</div>
            </div>
            <div class="pd-metric">
                <div class="pd-metric-label"><span class="pd-metric-dot positive"></span>Payments collected</div>
                <div class="pd-metric-value">{{ number_format((float) ($summary['payments_collected'] ?? 0), 2) }}</div>
                <div class="pd-metric-note">Recorded payments and down payments</div>
            </div>
        </section>
        <div class="pd-quickline" aria-label="Operational snapshot">
            <div class="pd-quickitem"><span>Customers</span><strong>{{ number_format((int) ($summary['customers'] ?? 0)) }}</strong></div>
            <div class="pd-quickitem"><span>Stock retail value</span><strong>{{ number_format((float) ($stock['retail_value'] ?? 0), 2) }}</strong></div>
            <div class="pd-quickitem"><span>Open installment balance</span><strong class="danger">{{ number_format((float) ($installments['remaining'] ?? 0), 2) }}</strong></div>
            <div class="pd-quickitem"><span>Low-stock items</span><strong class="{{ (int) ($stock['low_stock'] ?? 0) > 0 ? 'warn' : '' }}">{{ number_format((int) ($stock['low_stock'] ?? 0)) }}</strong></div>
        </div>

        {{-- One integrated section: chart and the table that explains the chart. --}}
        <section class="pd-panel">
            <div class="pd-panel-head">
                <div class="pd-heading-wrap"><h2 class="pd-section-title">Sales performance</h2><p class="pd-section-note">Revenue trend with the daily figures behind it.</p></div>
                <span class="pd-period-tag">Selected date range</span>
            </div>
            <div class="pd-split">
                <div class="pd-pane">
                    <h3 class="pd-pane-title">Revenue trend <span class="pd-tag pd-tag-primary">REVENUE / DAY</span></h3>
                    <p class="pd-pane-note">Track peaks and quieter trading days.</p>
                    <div wire:ignore class="pd-chart"><canvas id="dashboardSalesRevenue" aria-label="Daily sales revenue chart" role="img"></canvas></div>
                </div>
                <div class="pd-pane">
                    <div class="pd-table-heading"><div><h3 class="pd-table-title">Daily sales details</h3><p class="pd-table-note">{{ number_format($dailySales->total()) }} days with sales</p></div></div>
                    <div class="pd-table-wrap"><table class="pd-table">
                        <thead><tr><th>Date</th><th style="text-align:right">Orders</th><th style="text-align:right">Revenue</th></tr></thead>
                        <tbody>
                            @forelse ($dailySales as $day)
                                <tr><td class="pd-strong">{{ \Illuminate\Support\Carbon::parse($day['date'])->format('d M Y') }}</td><td class="pd-number-muted">{{ number_format((int) $day['orders']) }}</td><td class="pd-number">{{ number_format((float) $day['revenue'], 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="pd-no-data">No sales in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table></div>
                    @include('livewire.partials.dashboard-pagination', ['paginator' => $dailySales, 'table' => 'dailySales'])
                </div>
            </div>
        </section>

        <section class="pd-panel">
            <div class="pd-panel-head">
                <div class="pd-heading-wrap"><h2 class="pd-section-title">Product performance</h2><p class="pd-section-note">Compare best sellers with products that are moving slowly, including items with zero sales.</p></div>
                <div class="pd-account-summary"><span class="pd-account-chip">Products tracked <strong>{{ number_format(count($report['top_products'] ?? [])) }}</strong></span></div>
            </div>
            <div class="pd-product-layout">
                <div class="pd-product-chart">
                    <h3 class="pd-pane-title">Top products <span class="pd-tag pd-tag-primary">UNITS SOLD</span></h3>
                    <p class="pd-pane-note">Top eight products by quantity.</p>
                    <div wire:ignore class="pd-chart pd-chart-short"><canvas id="dashboardTopProducts" aria-label="Top selling products chart" role="img"></canvas></div>
                </div>
                <div class="pd-product-best">
                    <div class="pd-table-heading"><div><h3 class="pd-table-title">Best-selling products</h3><p class="pd-table-note">Ranked by units sold and sales revenue.</p></div><span class="pd-tag pd-tag-positive">BEST SELLERS</span></div>
                    <div class="pd-table-wrap"><table class="pd-table">
                        <thead><tr><th>Product</th><th style="text-align:right">Sold</th><th style="text-align:right">Revenue</th></tr></thead>
                        <tbody>
                            @forelse ($topProducts as $product)
                                <tr><td class="pd-strong" title="{{ $product['name'] }}">{{ \Illuminate\Support\Str::limit($product['name'], 32) }}</td><td class="pd-number-muted">{{ number_format((int) $product['sold_quantity']) }}</td><td class="pd-number">{{ number_format((float) $product['sales_revenue'], 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="pd-no-data">No products found.</td></tr>
                            @endforelse
                        </tbody>
                    </table></div>
                    @include('livewire.partials.dashboard-pagination', ['paginator' => $topProducts, 'table' => 'topProducts'])
                </div>
            </div>
            <div class="pd-bottom-pane">
                <div class="pd-table-heading"><div><h3 class="pd-table-title">Least-selling products</h3><p class="pd-table-note">Items with the lowest quantity sold in the selected period, including zero-sale items.</p></div><span class="pd-tag pd-tag-warning">SLOW MOVERS</span></div>
                <div class="pd-table-wrap"><table class="pd-table">
                    <thead><tr><th>Product</th><th style="text-align:right">Sold</th><th style="text-align:right">Current stock</th></tr></thead>
                    <tbody>
                        @forelse ($lowProducts as $product)
                            <tr><td class="pd-strong" title="{{ $product['name'] }}">{{ \Illuminate\Support\Str::limit($product['name'], 52) }}</td><td class="{{ (int) $product['sold_quantity'] === 0 ? 'pd-number pd-alert-number' : 'pd-number-muted' }}">{{ number_format((int) $product['sold_quantity']) }}</td><td class="pd-number-muted">{{ number_format((int) $product['current_quantity']) }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="pd-no-data">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
                @include('livewire.partials.dashboard-pagination', ['paginator' => $lowProducts, 'table' => 'lowProducts'])
            </div>
        </section>

        <section class="pd-panel">
            <div class="pd-panel-head">
                <div class="pd-heading-wrap"><h2 class="pd-section-title">Collections & installments</h2><p class="pd-section-note">Money collected in the selected period and accounts with a remaining balance.</p></div>
                <div class="pd-account-summary"><span class="pd-account-chip">Open accounts <strong>{{ number_format($openInstallments->total()) }}</strong></span><span class="pd-account-chip">Payments <strong>{{ number_format($payments->total()) }}</strong></span></div>
            </div>
            <div class="pd-split">
                <div class="pd-pane">
                    <div class="pd-payment-area">
                        <div>
                            <h3 class="pd-pane-title">Collection trend</h3><p class="pd-pane-note">Recorded payments by payment date.</p>
                            <div wire:ignore class="pd-chart pd-chart-short"><canvas id="dashboardPaymentsByDay" aria-label="Daily collection chart" role="img"></canvas></div>
                        </div>
                        <div>
                            <h3 class="pd-pane-title">Payment methods</h3><p class="pd-pane-note">Share by recorded method.</p>
                            <div wire:ignore class="pd-method-chart"><canvas id="dashboardPaymentMethods" aria-label="Payment methods chart" role="img"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="pd-pane">
                    <div class="pd-table-heading"><div><h3 class="pd-table-title">Recorded installment payments</h3><p class="pd-table-note">{{ number_format($payments->total()) }} payments in selected period</p></div></div>
                    <div class="pd-table-wrap"><table class="pd-table">
                        <thead><tr><th>Paid on</th><th>Installment</th><th>Method</th><th style="text-align:right">Amount</th></tr></thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr><td class="pd-strong" style="white-space:nowrap">{{ $payment['payment_date'] ? \Illuminate\Support\Carbon::parse($payment['payment_date'])->format('d M Y') : '—' }}</td><td>#{{ $payment['installment_id'] ?? '—' }}</td><td>{{ str_replace('_', ' ', $payment['method'] ?? '—') }}</td><td class="pd-number">{{ number_format((float) $payment['amount'], 2) }}</td></tr>
                            @empty
                                <tr><td colspan="4" class="pd-no-data">No payments in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table></div>
                    @include('livewire.partials.dashboard-pagination', ['paginator' => $payments, 'table' => 'payments'])
                </div>
            </div>
            <div class="pd-bottom-pane">
                <div class="pd-table-heading"><div><h3 class="pd-table-title">Outstanding installment accounts</h3><p class="pd-table-note">Accounts that still have a balance, ordered by start date.</p></div><span class="pd-tag pd-tag-danger">OPEN BALANCE: {{ number_format((float) ($installments['remaining'] ?? 0), 2) }}</span></div>
                <div class="pd-table-wrap"><table class="pd-table">
                    <thead><tr><th>Account</th><th>Start date</th><th style="text-align:right">Total with interest</th><th style="text-align:right">Remaining balance</th></tr></thead>
                    <tbody>
                        @forelse ($openInstallments as $installment)
                            <tr><td class="pd-strong">Installment #{{ $installment['id'] }}</td><td>{{ $installment['start_date'] ? \Illuminate\Support\Carbon::parse($installment['start_date'])->format('d M Y') : '—' }}</td><td class="pd-number-muted">{{ number_format((float) $installment['total_with_interest'], 2) }}</td><td class="pd-number pd-alert-number">{{ number_format((float) $installment['remaining_amount'], 2) }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="pd-no-data">No outstanding installment accounts.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
                @include('livewire.partials.dashboard-pagination', ['paginator' => $openInstallments, 'table' => 'installments'])
            </div>
        </section>

        <section class="pd-panel">
            <div class="pd-panel-head"><div class="pd-heading-wrap"><h2 class="pd-section-title">Team & purchasing</h2><p class="pd-section-note">See who is driving sales and which suppliers account for the most purchasing.</p></div></div>
            <div class="pd-split-equal">
                <div class="pd-pane">
                    <div class="pd-table-heading"><div><h3 class="pd-table-title">Employee performance</h3><p class="pd-table-note">Sorted by sales revenue.</p></div></div>
                    <div class="pd-table-wrap"><table class="pd-table">
                        <thead><tr><th>Employee</th><th style="text-align:right">Sales</th><th style="text-align:right">Revenue</th></tr></thead>
                        <tbody>
                            @forelse ($employees as $employee)
                                <tr><td class="pd-strong">{{ $employee['name'] ?? 'Unknown' }}</td><td class="pd-number-muted">{{ number_format((int) ($employee['sales_count'] ?? 0)) }}</td><td class="pd-number">{{ number_format((float) ($employee['sales_revenue'] ?? 0), 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="pd-no-data">No employee sales in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table></div>
                    @include('livewire.partials.dashboard-pagination', ['paginator' => $employees, 'table' => 'employees'])
                </div>
                <div class="pd-pane">
                    <div class="pd-table-heading"><div><h3 class="pd-table-title">Purchases by supplier</h3><p class="pd-table-note">Sorted by total purchase spending.</p></div></div>
                    <div class="pd-table-wrap"><table class="pd-table">
                        <thead><tr><th>Supplier</th><th style="text-align:right">Purchases</th><th style="text-align:right">Total spend</th></tr></thead>
                        <tbody>
                            @forelse ($suppliers as $supplier)
                                <tr><td class="pd-strong">{{ $supplier['name'] ?? 'Unknown supplier' }}</td><td class="pd-number-muted">{{ number_format((int) ($supplier['purchases_count'] ?? 0)) }}</td><td class="pd-number">{{ number_format((float) ($supplier['purchase_total'] ?? 0), 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="pd-no-data">No purchases in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table></div>
                    @include('livewire.partials.dashboard-pagination', ['paginator' => $suppliers, 'table' => 'suppliers'])
                </div>
            </div>
        </section>

        <p class="pd-footer-note">Gross profit is an estimate based on available purchase costs. Stock value reflects current inventory. The current data model contains installment start dates and actual payment dates, but no separate scheduled due-date field.</p>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @endassets

    @script
        <script>
            const initialDashboardChartData = @js($this->chartData);
            const dashboardChartInstances = {};
            // Locked semantic palette: blue = primary activity, teal = collections, amber = stock/purchases, red = outstanding risk.
            const dashboardPalette = ['#3F64E8', '#159782', '#C88B28', '#8170C7', '#D45C65', '#728096', '#42A6B9', '#AF7A56'];

            function renderDashboardChart(canvasId, type, labels, values, datasetLabel, options = {}) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || typeof Chart === 'undefined') return;
                if (dashboardChartInstances[canvasId]) dashboardChartInstances[canvasId].destroy();

                const isDoughnut = type === 'doughnut';
                const horizontal = options.indexAxis === 'y';
                const color = options.color ?? '#3F64E8';
                const chartValues = values ?? [];
                dashboardChartInstances[canvasId] = new Chart(canvas, {
                    type,
                    data: {
                        labels: labels ?? [],
                        datasets: [{
                            label: datasetLabel,
                            data: chartValues,
                            borderColor: isDoughnut ? '#FFFFFF' : color,
                            backgroundColor: isDoughnut ? chartValues.map((_, index) => dashboardPalette[index % dashboardPalette.length]) : (type === 'line' ? (options.fillColor ?? 'rgba(63,100,232,.10)') : color),
                            borderWidth: isDoughnut ? 3 : (type === 'line' ? 2.5 : 0),
                            borderRadius: type === 'bar' ? 5 : 0,
                            fill: type === 'line',
                            tension: .34,
                            pointRadius: type === 'line' ? 2 : 0,
                            pointHoverRadius: type === 'line' ? 4 : 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: options.indexAxis ?? 'x',
                        cutout: isDoughnut ? '73%' : undefined,
                        plugins: {
                            legend: { display: isDoughnut, position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 7, boxHeight: 7, padding: 11, color: '#778294', font: { size: 9, family: 'inherit' } } },
                            tooltip: { mode: 'index', intersect: false, padding: 10, backgroundColor: '#182230', titleFont: { size: 11 }, bodyFont: { size: 11 }, displayColors: true }
                        },
                        scales: isDoughnut ? {} : {
                            x: { beginAtZero: true, grid: { display: horizontal, color: 'rgba(140,151,169,.13)' }, border: { display: false }, ticks: { color: '#929bab', maxRotation: 0, autoSkip: true, precision: options.integer ? 0 : undefined, font: { size: 9 } } },
                            y: { beginAtZero: true, grid: { display: !horizontal, color: 'rgba(140,151,169,.13)' }, border: { display: false }, ticks: { color: '#929bab', precision: options.integer ? 0 : undefined, font: { size: 9 } } }
                        }
                    }
                });
            }

            function updateDashboardCharts(data) {
                if (!data) return;
                renderDashboardChart('dashboardSalesRevenue', 'line', data.sales?.labels, data.sales?.revenue, 'Revenue', { color: '#3F64E8', fillColor: 'rgba(63,100,232,.10)' });
                renderDashboardChart('dashboardTopProducts', 'bar', data.products?.labels, data.products?.quantity, 'Units sold', { color: '#3F64E8', indexAxis: 'y', integer: true });
                renderDashboardChart('dashboardPaymentsByDay', 'line', data.paymentsByDay?.labels, data.paymentsByDay?.totals, 'Collected', { color: '#159782', fillColor: 'rgba(21,151,130,.10)' });
                renderDashboardChart('dashboardPaymentMethods', 'doughnut', data.payments?.labels, data.payments?.totals, 'Payments');
            }

            updateDashboardCharts(initialDashboardChartData);
            $wire.on('dashboard-charts-updated', (event) => updateDashboardCharts(event.detail.chartData));
        </script>
    @endscript
</div>
