@props([
    'title',
    'subtitle' => null,
    'badge' => null,
    'backUrl' => null,
    'backText' => 'Back',
])

<div class="mb-8">

    @if($backUrl)
        <a
            href="{{ $backUrl }}"
            class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-indigo-600"
        >
            <svg
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M17 10a.75.75 0 01-.75.75H5.56l3.22 3.22a.75.75 0 11-1.06 1.06l-4.5-4.5a.75.75 0 010-1.06l4.5-4.5a.75.75 0 111.06 1.06l-3.22 3.22h10.69A.75.75 0 0117 10Z"
                    clip-rule="evenodd"
                />
            </svg>

            {{ $backText }}
        </a>
    @endif


    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">

        <div class="min-w-0">

            <div class="mb-3 flex items-center gap-3">

                @if(isset($icon))
                    {{ $icon }}
                @endif

                @if($badge)
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-600">
                        {{ $badge }}
                    </span>
                @endif

            </div>

            <h1 class="text-3xl font-bold tracking-tight text-slate-900">
                {{ $title }}
            </h1>

            @if($subtitle)
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    {{ $subtitle }}
                </p>
            @endif

        </div>


        @if(isset($actions))
    <div class="w-full shrink-0 lg:w-auto">
        {{ $actions }}
    </div>
@endif

    </div>

</div>