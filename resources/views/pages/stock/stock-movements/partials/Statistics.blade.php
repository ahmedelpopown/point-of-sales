    {{-- Statistics --}}
    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Total Movements
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['total_movements']) }}
            </p>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Incoming Quantity
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['incoming_quantity']) }}
            </p>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Outgoing Quantity
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['outgoing_quantity']) }}
            </p>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm font-medium text-gray-500">
                Today's Movements
            </p>

            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($this->statistics['today_movements']) }}
            </p>
        </div>

    </div>