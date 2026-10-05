<div class="mt-6 flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">

    <div>
        <h2 class="text-sm font-semibold text-gray-900">
            Order Files
        </h2>

        <p class="mt-1 text-xs text-gray-500">
            Import orders or export the current order list.
        </p>
    </div>

    <div class="flex flex-wrap gap-2">

        <label
            for="orders-import"
            class="inline-flex cursor-pointer items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            Import Excel
        </label>

        <input
            id="orders-import"
            type="file"
            wire:model="importFile"
            accept=".xlsx,.xls,.csv"
            class="hidden"
        >

        <button
            type="button"
            wire:click="importExcel"
            wire:loading.attr="disabled"
            wire:target="importExcel"
            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="importExcel">
                Import
            </span>

            <span wire:loading wire:target="importExcel">
                Importing...
            </span>
        </button>

        <button
            type="button"
            wire:click="exportExcel"
            wire:loading.attr="disabled"
            wire:target="exportExcel"
            class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100 disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="exportExcel">
                Export Excel
            </span>

            <span wire:loading wire:target="exportExcel">
                Exporting...
            </span>
        </button>

        <button
            type="button"
            wire:click="exportPdf"
            wire:loading.attr="disabled"
            wire:target="exportPdf"
            class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 disabled:opacity-50"
        >
            <span wire:loading.remove wire:target="exportPdf">
                Export PDF
            </span>

            <span wire:loading wire:target="exportPdf">
                Exporting...
            </span>
        </button>

        <button
            type="button"
            wire:click="downloadImportTemplate"
            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            Download Template
        </button>

    </div>

</div>