<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

    <div>
        <h1 class="text-2xl font-semibold text-gray-900">
            Orders
        </h1>

        <p class="mt-2 text-sm text-gray-700">
            Manage and filter all orders.
        </p>
    </div>

    <div class="flex flex-wrap gap-2">

        <a
            href="{{ route('orders.create') }}"
            class="inline-flex min-w-fit shrink-0 items-center justify-center whitespace-nowrap rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-indigo-700"
        >
            <svg
                class="mr-2 h-5 w-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"
                />
            </svg>

            Create New Order
        </a>

        <a
            href="{{ route('installments.create') }}"
            class="inline-flex min-w-fit shrink-0 items-center justify-center whitespace-nowrap rounded-xl bg-indigo-100 px-6 py-3 text-sm font-bold text-indigo-700 shadow-md transition hover:bg-indigo-200"
        >
            <svg
                class="mr-2 h-5 w-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4"
                />
            </svg>

            Create New Installment Order
        </a>

    </div>

</div>