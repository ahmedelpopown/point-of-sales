@if(count($selectedOrders))

    <div class="mt-6 flex flex-col gap-3 rounded-xl border border-indigo-100 bg-indigo-50 p-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <p class="text-sm font-semibold text-indigo-900">
                {{ count($selectedOrders) }}
                {{ count($selectedOrders) === 1 ? 'order' : 'orders' }}
                selected
            </p>

            <p class="mt-1 text-xs text-indigo-700">
                Selected orders are ready for bulk actions.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">

            <button
                type="button"
                wire:click="exportSelected"
                wire:loading.attr="disabled"
                wire:target="exportSelected"
                class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="exportSelected">
                    Export Selected
                </span>

                <span wire:loading wire:target="exportSelected">
                    Exporting...
                </span>
            </button>

            <button
                type="button"
                wire:click="deleteSelected"
                wire:confirm="Are you sure you want to delete the selected orders?"
                wire:loading.attr="disabled"
                wire:target="deleteSelected"
                class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="deleteSelected">
                    Delete Selected
                </span>

                <span wire:loading wire:target="deleteSelected">
                    Deleting...
                </span>
            </button>

            <button
                type="button"
                wire:click="clearSelection"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Clear
            </button>

        </div>

    </div>

@endif