<?php

use App\Models\InstallmentPlan;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Installment Plan')] class extends Component
{
    public string $name = '';
    public int|string $months_count = '';
    public float|int $interest_rate = 0;

    public function save(): void
    {
        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:installment_plans,name',
            ],

            'months_count' => [
                'required',
                'integer',
                'min:1',
            ],

            'interest_rate' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        InstallmentPlan::create($validated);

        session()->flash(
            'message',
            'Installment plan created successfully.'
        );

 
    }
};
?>

<div class="px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Create Installment Plan
            </h1>

            <p class="mt-2 text-sm text-gray-700">
                Create a new installment plan.
            </p>
        </div>

       

    </div>


    {{-- Error Message --}}
    @if(session()->has('error'))

        <div class="mt-6 rounded-xl bg-red-50 p-4">
            <p class="text-sm text-red-800">
                {{ session('error') }}
            </p>
        </div>

    @endif


    {{-- Form --}}
    <form
        wire:submit="save"
        class="mt-8 max-w-3xl rounded-2xl border border-gray-200 bg-white p-6 shadow-sm"
    >

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- Name --}}
            <div class="md:col-span-2">

                <label class="block text-sm font-medium text-gray-700">
                    Plan Name
                </label>

                <input
                    type="text"
                    wire:model="name"
                    placeholder="Example: 6 Months"
                    class="mt-1 block w-full rounded-xl border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Number of Installments --}}
        <div>
    <label
        for="months_count"
        class="block text-sm font-medium text-gray-700"
    >
        Number of Months
    </label>

    <input
        id="months_count"
        type="number"
        min="1"
        wire:model="months_count"
        class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        placeholder="e.g. 12"
    >

    @error('months_count')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>


            {{-- Interest Rate --}}
      <div>
    <label
        for="interest_rate"
        class="block text-sm font-medium text-gray-700"
    >
        Interest Rate (%)
    </label>

    <input
        id="interest_rate"
        type="number"
        step="0.01"
        min="0"
        wire:model="interest_rate"
        class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        placeholder="e.g. 15"
    >

    @error('interest_rate')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>

        </div>


        {{-- Actions --}}
        <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 pt-6">

         
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="inline-flex items-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
            >

                <span wire:loading.remove wire:target="save">
                    Create Plan
                </span>

                <span wire:loading wire:target="save">
                    Creating...
                </span>

            </button>

        </div>

    </form>

</div>