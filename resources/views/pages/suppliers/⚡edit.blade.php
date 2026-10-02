<?php

use App\Livewire\Forms\SupplierForm;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Supplier;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Supplier')] class extends Component
{
    public SupplierForm $form;

    public function mount(Supplier $supplier): void
    {
        $this->form->setSupplier($supplier);
    }

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
        $this->form->update();

        session()->flash(
            'message',
            'Supplier updated successfully.'
        );

        $this->redirectRoute('suppliers.index');
    }
};
?>

@include('pages.suppliers.form', [
    'title' => 'Edit Supplier',
    'subtitle' => 'Update supplier contact and location information.',
    'submitText' => 'Update Supplier',
    'loadingText' => 'Updating...',
    'governorates' => $this->governorates,
    'cities' => $this->cities,
])