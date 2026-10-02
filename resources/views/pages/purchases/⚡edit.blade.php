<?php

use App\Livewire\Concerns\HasPurchaseFormData;
use App\Livewire\Forms\PurchaseForm;
use App\Models\Purchase;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Purchase')] class extends Component
{
    use HasPurchaseFormData;

    public PurchaseForm $form;

    public function mount(Purchase $purchase): void
    {
        $this->form->setPurchase($purchase);

        $this->editingWarehouseId = $purchase->warehouse_id;
    }

    public function addItem(): void
    {
        $this->form->addItem();
    }

    public function removeItem(int $index): void
    {
        $this->form->removeItem($index);
    }

    public function save(): void
    {
        try {

            $this->form->update();

            session()->flash(
                'message',
                'Purchase updated successfully.'
            );

            $this->redirectRoute('purchases.index');

        } catch (\Throwable $e) {

            report($e);

            session()->flash(
                'error',
                $e->getMessage()
            );
        }
    }
};
?>

@include('pages.purchases.form', [
    'title' => 'Edit Purchase #' . $form->purchase?->id,
    'subtitle' => 'Update supplier, employee, warehouse, and purchase items.',
    'submitText' => 'Update Purchase',
    'loadingText' => 'Updating...',
])