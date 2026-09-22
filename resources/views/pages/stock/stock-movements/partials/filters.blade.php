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