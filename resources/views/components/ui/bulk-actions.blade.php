@props([
    'count' => 0,
])

<div class="mt-4 rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex items-center gap-3">

            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">

                <svg
                    class="h-4 w-4"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v9.5A2.75 2.75 0 0115.25 19h-10A2.75 2.75 0 012.5 16.25v-9.5A2.75 2.75 0 015.25 4h.25V2.75A.75.75 0 015.75 2ZM4 8v8.25c0 .69.56 1.25 1.25 1.25h10c.69 0 1.25-.56 1.25-1.25V8H4Z"
                        clip-rule="evenodd"
                    />
                </svg>

            </div>

            <div>
                <p class="text-sm font-semibold text-indigo-900">
                    {{ $count }} product{{ $count > 1 ? 's' : '' }} selected
                </p>

                <p class="text-xs text-indigo-600">
                    Choose an action for the selected products.
                </p>
            </div>

        </div>

        <div class="flex flex-wrap gap-2">
            {{ $slot }}
        </div>

    </div>

</div>