@props([
    'title',
    'text',
])

<section class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-6">

    <div class="flex items-start gap-3">

        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">

            <svg
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <circle
                    cx="12"
                    cy="12"
                    r="9"
                />

                <path
                    stroke-linecap="round"
                    d="M12 10.5v5"
                />

                <path
                    stroke-linecap="round"
                    d="M12 7.5h.01"
                />
            </svg>

        </div>


        <div>
            <h3 class="text-sm font-semibold text-slate-900">
                {{ $title }}
            </h3>

            <p class="mt-2 text-sm leading-6 text-slate-600">
                {{ $text }}
            </p>
        </div>

    </div>

</section>