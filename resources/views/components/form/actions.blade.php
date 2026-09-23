@props([
    'cancelUrl',
    'cancelText' => 'Cancel',
    'submitText' => 'Save',
    'loadingText' => 'Saving...',
    'loadingTarget' => 'save',
])

<div class="sticky bottom-4 flex flex-col-reverse gap-3 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-lg shadow-slate-900/5 backdrop-blur sm:flex-row sm:items-center sm:justify-between">

    <div class="text-xs text-slate-500">
        {{ $slot }}
    </div>


    <div class="flex items-center justify-end gap-3">

        <a
            href="{{ $cancelUrl }}"
            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-500/10"
        >
            {{ $cancelText }}
        </a>


        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="{{ $loadingTarget }}"
            class="inline-flex min-w-[150px] items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 disabled:cursor-not-allowed disabled:opacity-60"
        >

            <svg
                wire:loading
                wire:target="{{ $loadingTarget }}"
                class="h-4 w-4 animate-spin"
                viewBox="0 0 24 24"
                fill="none"
                aria-hidden="true"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                />

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                />
            </svg>


            <span
                wire:loading.remove
                wire:target="{{ $loadingTarget }}"
            >
                {{ $submitText }}
            </span>


            <span
                wire:loading
                wire:target="{{ $loadingTarget }}"
            >
                {{ $loadingText }}
            </span>

        </button>

    </div>

</div>