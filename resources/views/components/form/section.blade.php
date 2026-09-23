@props([
    'title',
    'description' => null,
])

<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

    <div class="border-b border-slate-100 px-6 py-5">

        <div class="flex items-start gap-4">

            @if(isset($icon))
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    {{ $icon }}
                </div>
            @endif


            <div class="min-w-0">

                <h2 class="text-base font-semibold text-slate-900">
                    {{ $title }}
                </h2>

                @if($description)
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $description }}
                    </p>
                @endif

            </div>

        </div>

    </div>


    <div class="px-6 py-6">
        {{ $slot }}
    </div>

</section>