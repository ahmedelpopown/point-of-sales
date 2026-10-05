<div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

    <div class="mb-5 flex items-center justify-between">

        <div>
            <h2 class="text-lg font-semibold text-gray-900">
                Filters
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Filter orders by customer, employee, shop, product, payment, date, or total.
            </p>
        </div>

        <button
            type="button"
            wire:click="resetFilters"
            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
        >
            Reset
        </button>

    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">

        <div class="lg:col-span-3">
            <x-form.input
                label="Search"
                name="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Order ID, customer, employee, or product..."
            />
        </div>

        <x-form.searchable-select
            label="Customer"
            model="customerId"
            :options="$this->users"
            option-value="id"
            option-label="name"
            placeholder="All Customers"
        />

        <x-form.searchable-select
            label="Employee"
            model="employeeId"
            :options="$this->employees"
            option-value="id"
            option-label="full_name"
            placeholder="All Employees"
        />

        <x-form.searchable-select
            label="Shop"
            model="shopId"
            :options="$this->shops"
            option-value="id"
            option-label="name"
            placeholder="All Shops"
        />

        <x-form.searchable-select
            label="Product"
            model="productId"
            :options="$this->products"
            option-value="id"
            option-label="name"
            placeholder="All Products"
        />

        <div>
            <label class="block text-sm font-medium text-gray-700">
                Payment Method
            </label>

            <div class="mt-2 flex gap-6">

                <label class="inline-flex items-center gap-2">
                    <input
                        type="checkbox"
                        value="cash"
                        wire:model.live="paymentMethods"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    >

                    <span class="text-sm text-gray-700">
                        Cash
                    </span>
                </label>

                <label class="inline-flex items-center gap-2">
                    <input
                        type="checkbox"
                        value="installment"
                        wire:model.live="paymentMethods"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    >

                    <span class="text-sm text-gray-700">
                        Installment
                    </span>
                </label>

            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">
                Per Page
            </label>

            <select
                wire:model.live="perPage"
                class="mt-1 block w-full rounded-xl border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <x-form.input
            label="Date From"
            name="dateFrom"
            type="date"
            wire:model.live="dateFrom"
        />

        <x-form.input
            label="Date To"
            name="dateTo"
            type="date"
            wire:model.live="dateTo"
        />

        <x-form.input
            label="Minimum Total"
            name="minTotal"
            type="number"
            min="0"
            step="0.01"
            placeholder="0.00"
            wire:model.live="minTotal"
        />

        <x-form.input
            label="Maximum Total"
            name="maxTotal"
            type="number"
            min="0"
            step="0.01"
            placeholder="0.00"
            wire:model.live="maxTotal"
        />

    </div>

</div>