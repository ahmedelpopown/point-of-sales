@if(session()->has('error'))

    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-4">

        <p class="text-sm font-medium text-red-800">
            {{ session('error') }}
        </p>

    </div>

@endif

@if(session()->has('message'))

    <div class="mt-4 rounded-lg border border-green-200 bg-green-50 p-4">

        <p class="text-sm font-medium text-green-800">
            {{ session('message') }}
        </p>

    </div>

@endif