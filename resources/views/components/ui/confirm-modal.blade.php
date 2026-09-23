@props([
    'show' => false,
    'title' => 'Confirm action',
    'message' => 'Are you sure?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmAction' => null,
    'cancelAction' => null,
])

@if($show)

    <div
        class="fixed inset-0 z-50"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-modal-title"
    >

        <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm"></div>

        <div class="fixed inset-0 overflow-y-auto p-4">

            <div class="flex min-h-full items-center justify-center">

                <div class="relative w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

                    <div class="p-6">

                        <div class="flex items-start gap-4">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600">

                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 9v3.75m0 3.001.008-.008"
                                    />

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M10.3 3.4 2.9 16.2A2 2 0 004.63 19h14.74a2 2 0 001.73-2.8L13.7 3.4a2 2 0 00-3.4 0Z"
                                    />
                                </svg>

                            </div>

                            <div>

                                <h3
                                    id="confirm-modal-title"
                                    class="text-base font-semibold text-slate-900"
                                >
                                    {{ $title }}
                                </h3>

                                <p class="mt-2 text-sm leading-6 text-slate-500">
                                    {{ $message }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="flex justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">

                        <button
                            type="button"
                            wire:click="{{ $cancelAction }}"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            {{ $cancelText }}
                        </button>

                        <button
                            type="button"
                            wire:click="{{ $confirmAction }}"
                            wire:loading.attr="disabled"
                            wire:target="{{ $confirmAction }}"
                            class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ $confirmText }}
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endif