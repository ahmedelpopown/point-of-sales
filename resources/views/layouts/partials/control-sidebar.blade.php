<aside class="control-sidebar control-sidebar-dark">
    <div class="p-3">
        <h5 class="mb-1">Quick tools</h5>
        <p class="text-sm text-muted mb-4">Shortcuts for your POS workspace.</p>

        <div class="d-flex align-items-center mb-3">
            <div class="mr-3"><i class="fas fa-user-circle fa-2x"></i></div>
            <div class="min-width-0">
                <div class="font-weight-bold text-truncate">{{ auth()->guard('employee')->user()?->full_name ?? 'Employee' }}</div>
                <small class="text-muted">{{ auth()->guard('employee')->user()?->getRoleNames()->first() ?? 'POS employee' }}</small>
            </div>
        </div>

        <div class="dropdown-divider"></div>
        <div class="nav nav-pills nav-sidebar flex-column">
            @if (\Illuminate\Support\Facades\Route::has('dashboard'))
                <a href="{{ route('dashboard') }}" class="nav-link"><i class="nav-icon fas fa-chart-pie"></i><p>Dashboard</p></a>
            @endif
            @if (\Illuminate\Support\Facades\Route::has('global-search.index'))
                <a href="{{ route('global-search.index') }}" class="nav-link"><i class="nav-icon fas fa-search"></i><p>Global search</p></a>
            @endif
            @if (\Illuminate\Support\Facades\Route::has('products.index'))
                <a href="{{ route('products.index') }}" class="nav-link"><i class="nav-icon fas fa-box"></i><p>Products</p></a>
            @endif
            @if (\Illuminate\Support\Facades\Route::has('orders.index'))
                <a href="{{ route('orders.index') }}" class="nav-link"><i class="nav-icon fas fa-receipt"></i><p>Sales / Orders</p></a>
            @endif
            @if (\Illuminate\Support\Facades\Route::has('installments.index'))
                <a href="{{ route('installments.index') }}" class="nav-link"><i class="nav-icon fas fa-calendar-check"></i><p>Installments</p></a>
            @endif
        </div>

        <div class="dropdown-divider"></div>
        <p class="text-xs text-muted mb-0">Menu items appear when their named routes exist.</p>
    </div>
</aside>
