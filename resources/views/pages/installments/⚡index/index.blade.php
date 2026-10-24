
<div class="px-4 sm:px-6 lg:px-8">

    <div class="sm:flex sm:items-center">

        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">
                Installments
            </h1>

            <p class="mt-2 text-sm text-gray-700">
                Manage customer installment contracts and payments.
            </p>
        </div>

        <div class="mt-4 sm:mt-0">
            <a
                href="{{ route('installments.create') }}"
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                Add Installment
            </a>
        </div>

    </div>

    @if (session()->has('message'))
        <div class="mt-4 rounded-md bg-green-50 p-4">
            <p class="text-sm text-green-800">
                {{ session('message') }}
            </p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mt-4 rounded-md bg-red-50 p-4">
            <p class="text-sm text-red-800">
                {{ session('error') }}
            </p>
        </div>
    @endif

    <div class="mt-8 flex flex-col gap-4 sm:flex-row">

        <input
            wire:model.live.debounce.300ms="search"
            type="search"
            placeholder="Search installment plan..."
            aria-label="Search installment plans"
            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >

        <select
            wire:model.live="perPage"
            aria-label="Rows per page"
            class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-40"
        >
            <option value="10">10 per page</option>
            <option value="25">25 per page</option>
            <option value="50">50 per page</option>
        </select>

    </div>

    @if(count($selected) > 0)
        <div class="mt-6">
            <x-ui.bulk-actions :count="count($selected)" item-label="installment" items-label="installments">
                <x-ui.action-button wire:click="clearBulkSelection">Clear selection</x-ui.action-button>
            </x-ui.bulk-actions>
        </div>
    @endif

    <div class="mt-8 overflow-x-auto rounded-lg bg-white shadow">

        <table class="min-w-full divide-y divide-gray-300">

            <thead class="bg-gray-50">
                <tr>
                    <th class="w-12 px-4 py-3">
                        <input
                            type="checkbox"
                            wire:model.live="selectAll"
                            aria-label="Select all installments on this page"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        <button wire:click="sortBy('id')" type="button">
                            ID
                        </button>
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Order
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Plan
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        <button
                            wire:click="sortBy('total_with_interest')"
                            type="button"
                        >
                            Total
                        </button>
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Down Payment
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        <button
                            wire:click="sortBy('remaining_amount')"
                            type="button"
                        >
                            Remaining
                        </button>
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        <button
                            wire:click="sortBy('created_at')"
                            type="button"
                        >
                            Created At
                        </button>
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">

                @forelse ($this->installments as $installment)
                    <tr wire:key="installment-{{ $installment->id }}">

                        <td class="whitespace-nowrap px-4 py-4 text-sm">
                            <input
                                type="checkbox"
                                wire:model.live="selected"
                                value="{{ $installment->id }}"
                                aria-label="Select installment {{ $installment->id }}"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm">
                            #{{ $installment->id }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm">
                            #{{ $installment->order_id }}
                        </td>

                        <td class="px-4 py-4 text-sm">
                            {{ $installment->installmentPlan?->name ?? 'N/A' }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold">
                            {{ number_format((float) $installment->total_with_interest, 2) }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm">
                            {{ number_format((float) $installment->down_payment, 2) }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-red-600">
                            {{ number_format((float) $installment->remaining_amount, 2) }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm">
                            {{ $installment->created_at?->format('Y-m-d') }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-4 text-sm">
                            <div class="flex gap-3">

                                <a
                                    href="{{ route('installments.show', $installment) }}"
                                    class="text-green-600 hover:text-green-800"
                                >
                                    Show
                                </a>

                                <a
                                    href="{{ route('installments.edit', $installment) }}"
                                    class="text-indigo-600 hover:text-indigo-800"
                                >
                                    Edit
                                </a>

                                <button
                                    type="button"
                                    wire:click="deleteInstallment({{ $installment->id }})"
                                    wire:confirm="Are you sure you want to delete this installment?"
                                    wire:loading.attr="disabled"
                                    class="text-red-600 hover:text-red-800 disabled:opacity-50"
                                >
                                    Delete
                                </button>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="9"
                            class="px-4 py-8 text-center text-gray-500"
                        >
                            No installments found.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4">
        {{ $this->installments->links() }}
    </div>

</div>
