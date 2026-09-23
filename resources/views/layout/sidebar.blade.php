<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 pos-sidebar">

    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img
            src="{{ asset('dist/img/AdminLTELogo.png') }}"
            alt="POS Logo"
            class="brand-image img-circle elevation-3"
            style="opacity: .9">

        <span class="brand-text font-weight-light">
            Point Of Sales
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">

            <div class="image">
                <img
                    src="{{ asset('dist/img/user2-160x160.jpg') }}"
                    class="img-circle elevation-2"
                    alt="Employee">
            </div>

            <div class="info">

                <a href="#" class="d-block">
                    {{ auth()->guard('employee')->user()?->full_name ?? 'Employee' }}
                </a>

                @if(auth()->guard('employee')->check())
                <small class="text-white-50">
                    {{ auth()->guard('employee')->user()->getRoleNames()->first() ?? 'Employee' }}
                </small>
                @endif

            </div>


        </div>
<!--  -->
        <!-- Sidebar Search -->
        <div class="form-inline mb-2">
            <div
                class="input-group"
                data-widget="sidebar-search">

                <input
                    class="form-control form-control-sidebar"
                    type="search"
                    placeholder="Search"
                    aria-label="Search">

                <div class="input-group-append">

                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>

                </div>

            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">

            <ul
                class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                {{-- ===================================================== --}}
                {{-- Dashboard --}}
                {{-- ===================================================== --}}

                @can('view dashboard')

                <li class="nav-item">

                    <a
                        href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-tachometer-alt"></i>

                        <p>
                            Dashboard
                        </p>

                    </a>

                </li>

                @endcan

                @canany([
                'view roles',
                'create roles',
                'update roles',
                'delete roles'
                ])


                <li
                    class="nav-item
                        {{ request()->routeIs('roles.*') ? 'menu-open' : '' }}">

                    <a
                        href="#"
                        class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-box"></i>

                        <p>
                            Roles

                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        {{-- All Products --}}

                        @can('view roles')

                        <li class="nav-item">

                            <a
                                href="{{ route('admin.roles') }}"
                                class="nav-link {{ request()->routeIs('admin.roles') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    All Roles
                                </p>

                            </a>

                        </li>

                        @endcan


                        {{-- Create Product --}}

                        @can('create role')

                        <li class="nav-item">

                            <a
                                href="{{ route('admin.roles.permissions') }}"
                                class="nav-link {{ request()->routeIs('admin.roles.permissions') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    Create Roles
                                </p>

                            </a>

                        </li>

                        @endcan

                    </ul>

                </li>

                @endcanany

                {{-- ===================================================== --}}
                {{-- Products --}}
                {{-- ===================================================== --}}

                @canany([
                'view products',
                'create products',
                'update products',
                'delete products'
                ])

                <li
                    class="nav-item
                        {{ request()->routeIs('products.*') ? 'menu-open' : '' }}">

                    <a
                        href="#"
                        class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-box"></i>

                        <p>
                            Products

                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        {{-- All Products --}}

                        @can('view products')

                        <li class="nav-item">

                            <a
                                href="{{ route('products.index') }}"
                                class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    All Products
                                </p>

                            </a>

                        </li>

                        @endcan


                        {{-- Create Product --}}

                        @can('create products')

                        <li class="nav-item">

                            <a
                                href="{{ route('products.create') }}"
                                class="nav-link {{ request()->routeIs('products.create') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    Create Product
                                </p>

                            </a>

                        </li>

                        @endcan

                    </ul>

                </li>

                @endcanany


                {{-- ===================================================== --}}
                {{-- Users --}}
                {{-- ===================================================== --}}

                @canany([
                'view users',
                'create users',
                'update users',
                'delete users'
                ])

                <li
                    class="nav-item
                        {{ request()->routeIs('users.*') ? 'menu-open' : '' }}">

                    <a
                        href="#"
                        class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-users"></i>

                        <p>
                            Users

                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        @can('view users')

                        <li class="nav-item">

                            <a
                                href="{{ route('users.index') }}"
                                class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    All Users
                                </p>

                            </a>

                        </li>

                        @endcan


                        @can('create users')

                        <li class="nav-item">

                            <a
                                href="{{ route('users.create') }}"
                                class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    Create User
                                </p>

                            </a>

                        </li>

                        @endcan

                    </ul>

                </li>

                @endcanany


                {{-- ===================================================== --}}
                {{-- Employees --}}
                {{-- ===================================================== --}}

                @canany([
                'view employees',
                'create employees',
                'update employees',
                'delete employees'
                ])

                <li
                    class="nav-item
                        {{ request()->routeIs('employees.*') ? 'menu-open' : '' }}">

                    <a
                        href="#"
                        class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-user-tie"></i>

                        <p>
                            Employees

                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        @can('view employees')

                        <li class="nav-item">

                            <a
                                href="{{ route('employees.index') }}"
                                class="nav-link {{ request()->routeIs('employees.index') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    All Employees
                                </p>

                            </a>

                        </li>

                        @endcan


                        @can('create employees')

                        <li class="nav-item">

                            <a
                                href="{{ route('employees.create') }}"
                                class="nav-link {{ request()->routeIs('employees.create') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    Create Employee
                                </p>

                            </a>

                        </li>

                        @endcan

                    </ul>

                </li>

                @endcanany


                {{-- ===================================================== --}}
                {{-- Suppliers --}}
                {{-- ===================================================== --}}

                @canany([
                'view suppliers',
                'create suppliers',
                'update suppliers',
                'delete suppliers'
                ])

                <li
                    class="nav-item
                        {{ request()->routeIs('suppliers.*') ? 'menu-open' : '' }}">

                    <a
                        href="#"
                        class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-truck"></i>

                        <p>
                            Suppliers

                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        @can('view suppliers')

                        <li class="nav-item">

                            <a
                                href="{{ route('suppliers.index') }}"
                                class="nav-link {{ request()->routeIs('suppliers.index') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    All Suppliers
                                </p>

                            </a>

                        </li>

                        @endcan


                        @can('create suppliers')

                        <li class="nav-item">

                            <a
                                href="{{ route('suppliers.create') }}"
                                class="nav-link {{ request()->routeIs('suppliers.create') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    Create Supplier
                                </p>

                            </a>

                        </li>

                        @endcan

                    </ul>

                </li>

                @endcanany


                {{-- ===================================================== --}}
                {{-- Purchases --}}
                {{-- ===================================================== --}}

                @canany([
                'view purchases',
                'create purchases',
                'update purchases',
                'delete purchases'
                ])

                <li
                    class="nav-item
                        {{ request()->routeIs('purchases.*') ? 'menu-open' : '' }}">

                    <a
                        href="#"
                        class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-shopping-cart"></i>

                        <p>
                            Purchases

                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        @can('view purchases')

                        <li class="nav-item">

                            <a
                                href="{{ route('purchases.index') }}"
                                class="nav-link {{ request()->routeIs('purchases.index') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    All Purchases
                                </p>

                            </a>

                        </li>

                        @endcan


                        @can('create purchases')

                        <li class="nav-item">

                            <a
                                href="{{ route('purchases.create') }}"
                                class="nav-link {{ request()->routeIs('purchases.create') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    Create Purchase
                                </p>

                            </a>

                        </li>

                        @endcan

                    </ul>

                </li>

                @endcanany


                {{-- ===================================================== --}}
                {{-- Orders --}}
                {{-- ===================================================== --}}

                @canany([
                'view orders',
                'create orders',
                'update orders',
                'delete orders'
                ])

                <li
                    class="nav-item
                        {{ request()->routeIs('orders.*') ? 'menu-open' : '' }}">

                    <a
                        href="#"
                        class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-file-invoice"></i>

                        <p>
                            Orders

                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        @can('view orders')

                        <li class="nav-item">

                            <a
                                href="{{ route('orders.index') }}"
                                class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    All Orders
                                </p>

                            </a>

                        </li>

                        @endcan


                        @can('create orders')

                        <li class="nav-item">

                            <a
                                href="{{ route('orders.create') }}"
                                class="nav-link {{ request()->routeIs('orders.create') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    Create Order
                                </p>

                            </a>

                        </li>

                        @endcan

                    </ul>

                </li>

                @endcanany


                {{-- ===================================================== --}}
                {{-- Installments --}}
                {{-- ===================================================== --}}

                @canany([
                'view installments',
                
                'update installments',
                'delete installments'
                ])

                <li
                    class="nav-item
                        {{ request()->routeIs('installments.*') ? 'menu-open' : '' }}">

                    <a
                        href="#"
                        class="nav-link {{ request()->routeIs('installments.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-money-check-alt"></i>

                        <p>
                            Installments

                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        @can('view installments')

                        <li class="nav-item">

                            <a
                                href="{{ route('installments.index') }}"
                                class="nav-link {{ request()->routeIs('installments.index') ? 'active' : '' }}">

                                <i class="far fa-circle nav-icon"></i>

                                <p>
                                    All Installments
                                </p>

                            </a>

                        </li>

                        @endcan

 

                    </ul>

                </li>

                @endcanany


                {{-- ===================================================== --}}
                {{-- Stock --}}
                {{-- ===================================================== --}}
         
@can('adjust stock')

    <li
        class="nav-item
        {{ request()->routeIs('stock.*') ? 'menu-open' : '' }}"
    >

        <a
            href="#"
            class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}"
        >

            <i class="nav-icon fas fa-boxes"></i>

            <p>
                Stock
                <i class="right fas fa-angle-left"></i>
            </p>

        </a>

        <ul class="nav nav-treeview">

            {{-- Stock Overview --}}
            <li class="nav-item">

                <a
                    href="{{ route('stock.index') }}"
                    class="nav-link {{ request()->routeIs('stock.index') ? 'active' : '' }}"
                >

                    <i class="far fa-circle nav-icon"></i>

                    <p>
                        Stock Overview
                    </p>

                </a>

            </li>

            {{-- Warehouses --}}
            <li class="nav-item">

                <a
                    href="{{ route('stock.warehouses') }}"
                    class="nav-link {{ request()->routeIs('stock.warehouses') ? 'active' : '' }}"
                >

                    <i class="far fa-circle nav-icon"></i>

                    <p>
                        Warehouses
                    </p>

                </a>

            </li>

            {{-- Shops --}}
            <li class="nav-item">

                <a
                    href="{{ route('stock.shops') }}"
                    class="nav-link {{ request()->routeIs('stock.shops') ? 'active' : '' }}"
                >

                    <i class="far fa-circle nav-icon"></i>

                    <p>
                        Shops
                    </p>

                </a>

            </li>

            {{-- Stock Movements --}}
            <li class="nav-item">

                <a
                    href="{{ route('stock.stock-movements') }}"
                    class="nav-link {{ request()->routeIs('stock.stock-movements') ? 'active' : '' }}"
                >

                    <i class="far fa-circle nav-icon"></i>

                    <p>
                        Stock Movements
                    </p>

                </a>

            </li>

            {{-- Transfers --}}
            <li class="nav-item">

                <a
                    href="{{ route('stock.transfers') }}"
                    class="nav-link {{ request()->routeIs('stock.transfers') ? 'active' : '' }}"
                >

                    <i class="far fa-circle nav-icon"></i>

                    <p>
                        Stock Transfers
                    </p>

                </a>

            </li>

        </ul>

    </li>

@endcan


                
                <!-- view stock adjst stock -->
   {{-- Stock Movements --}}
            @can('view stock movements')

                <li class="nav-item">
                    <a
                        href="{{ route('stock.stock-movements') }}"
                        class="nav-link
                        {{ request()->routeIs('stock.movements') ? 'active' : '' }}"
                    >

                        <i class="far fa-circle nav-icon"></i>

                        <p>
                            Stock Movements
                        </p>

                    </a>

                </li>

            @endcan


            {{-- Stock Transfer --}}
            @can('transfer stock')

                <li class="nav-item">

                    <a
                        href="{{ route('stock.transfers') }}"
                        class="nav-link
                        {{ request()->routeIs('stock.transfers') ? 'active' : '' }}"
                    >

                        <i class="far fa-circle nav-icon"></i>

                        <p>
                            Stock Transfer
                        </p>

                    </a>

                </li>

            @endcan
                {{-- ===================================================== --}}
                {{-- Reports --}}
                {{-- ===================================================== --}}

                @can('view reports')

                <li class="nav-item">

                    <a
                        href="{{ route('reports.index') }}"
                        class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">

                        <i class="nav-icon fas fa-chart-bar"></i>

                        <p>
                            Reports
                        </p>

                    </a>

                </li>

                @endcan

            </ul>

        </nav>

    </div>

</aside>


<style>
    /*
    |--------------------------------------------------------------------------
    | Sidebar Background
    |--------------------------------------------------------------------------
    */

    .pos-sidebar {
        background: #172033 !important;
    }

    .pos-sidebar .brand-link {
        background: #111827 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .pos-sidebar .brand-text {
        color: #ffffff !important;
        font-weight: 500 !important;
    }


    /*
    |--------------------------------------------------------------------------
    | User Panel
    |--------------------------------------------------------------------------
    */

    .pos-sidebar .user-panel {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .pos-sidebar .user-panel .info a {
        color: #ffffff;
        font-weight: 500;
    }


    /*
    |--------------------------------------------------------------------------
    | Navigation Links
    |--------------------------------------------------------------------------
    */

    .pos-sidebar .nav-sidebar .nav-link {
        color: #cbd5e1;
        border-radius: 8px;
        margin: 2px 8px;
        transition: all 0.2s ease;
    }

    .pos-sidebar .nav-sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.07);
        color: #ffffff;
    }


    /*
    |--------------------------------------------------------------------------
    | Active Parent / Child
    |--------------------------------------------------------------------------
    */

    .pos-sidebar .nav-sidebar>.nav-item>.nav-link.active {
        background: #2563eb !important;
        color: #ffffff !important;
        box-shadow: 0 3px 10px rgba(37, 99, 235, 0.25);
    }

    .pos-sidebar .nav-treeview .nav-link.active {
        background: rgba(37, 99, 235, 0.18) !important;
        color: #ffffff !important;
    }

    .pos-sidebar .nav-treeview .nav-link.active .nav-icon {
        color: #60a5fa !important;
    }


    /*
    |--------------------------------------------------------------------------
    | Icons
    |--------------------------------------------------------------------------
    */

    .pos-sidebar .nav-icon {
        color: #94a3b8;
    }

    .pos-sidebar .nav-link.active .nav-icon {
        color: #ffffff;
    }


    /*
    |--------------------------------------------------------------------------
    | Tree View
    |--------------------------------------------------------------------------
    */

    .pos-sidebar .nav-treeview {
        padding-left: 6px;
    }

    .pos-sidebar .nav-treeview .nav-link {
        font-size: 0.92rem;
    }


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    .pos-sidebar .form-control-sidebar {
        background: #111827;
        border-color: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

    .pos-sidebar .form-control-sidebar::placeholder {
        color: #94a3b8;
    }

    .pos-sidebar .btn-sidebar {
        background: #111827;
        color: #94a3b8;
        border-color: rgba(255, 255, 255, 0.08);
    }
</style>