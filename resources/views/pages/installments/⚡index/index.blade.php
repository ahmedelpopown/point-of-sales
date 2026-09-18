
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
                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white"
            >
                Add Installment
            </a>

        </div>

    </div>

    @if(session()->has('message'))

        <div class="mt-4 rounded-md bg-green-50 p-4">
            <p class="text-sm text-green-800">
                {{ session('message') }}
            </p>
        </div>

    @endif

    @if(session()->has('error'))

        <div class="mt-4 rounded-md bg-red-50 p-4">
            <p class="text-sm text-red-800">
                {{ session('error') }}
            </p>
        </div>

    @endif

    <div class="mt-8">

        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Search installment plan..."
            class="block w-full rounded-lg border-gray-300"
        >

    </div>

    <div class="mt-8 overflow-hidden rounded-lg bg-white shadow">

        <table class="min-w-full divide-y divide-gray-300">

            <thead class="bg-gray-50">

                <tr>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        ID
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Order
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Plan
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Total
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Down Payment
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Remaining
                    </th>

                    <th class="px-4 py-3 text-left text-sm font-semibold">
                        Actions
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-gray-200">

                @forelse($this->installments as $installment)

                    <tr>

                        <td class="px-4 py-4 text-sm">
                            #{{ $installment->id }}
                        </td>

                        <td class="px-4 py-4 text-sm">
                            #{{ $installment->order_id }}
                        </td>

                        <td class="px-4 py-4 text-sm">
                            {{ $installment->installmentPlan->name ?? 'N/A' }}
                        </td>

                        <td class="px-4 py-4 text-sm font-semibold">
                            {{ number_format($installment->total_with_interest, 2) }}
                        </td>

                        <td class="px-4 py-4 text-sm">
                            {{ number_format($installment->down_payment, 2) }}
                        </td>

                        <td class="px-4 py-4 text-sm font-semibold text-red-600">
                            {{ number_format($installment->remaining_amount, 2) }}
                        </td>

                        <td class="px-4 py-4">

                            <div class="flex gap-3">

                                <a
                                    href="{{ route('installments.show', $installment) }}"
                                    class="text-green-600"
                                >
                                    Show
                                </a>

                                <a
                                    href="{{ route('installments.edit', $installment) }}"
                                    class="text-indigo-600"
                                >
                                    Edit
                                </a>

                                <button
                                    wire:click="deleteInstallment({{ $installment->id }})"
                                    wire:confirm="Are you sure?"
                                    class="text-red-600"
                                >
                                    Delete
                                </button>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
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