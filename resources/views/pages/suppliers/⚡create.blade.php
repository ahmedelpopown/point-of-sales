<?php

use App\Livewire\Forms\SupplierForm;
use App\Models\City;
use App\Models\Governorate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Supplier')] class extends Component
{
    public SupplierForm $form;

    #[Computed]
    public function governorates()
    {
        return Governorate::query()
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function cities()
    {
        if (!$this->form->governorate_id) {
            return collect();
        }

        return City::query()
            ->where('governorate_id', $this->form->governorate_id)
            ->orderBy('name')
            ->get();
    }

    public function updated($property): void
    {
        if ($property === 'form.governorate_id') {
            $this->form->city_id = null;
        }
    }

    public function save(): void
    {
        $this->form->store();

        session()->flash(
            'message',
            'Supplier created successfully.'
        );

        $this->redirectRoute('suppliers.index');
    }
};
?>

@include('pages.suppliers.form', [
    'title' => 'Create Supplier',
    'subtitle' => 'Create a new supplier and add their contact and location information.',
    'submitText' => 'Create Supplier',
    'loadingText' => 'Creating...',
    'governorates' => $this->governorates,
    'cities' => $this->cities,
])