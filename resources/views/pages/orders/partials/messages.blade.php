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