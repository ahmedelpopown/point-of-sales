<?php

use App\Livewire\Concerns\HasPurchaseFormData;
use App\Livewire\Forms\PurchaseForm;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create Purchase')] class extends Component
{
    use HasPurchaseFormData;

    public PurchaseForm $form;

    public function mount(): void
    {
        $this->form->addItem();
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

            $this->form->store();

            session()->flash(
                'message',
                'Purchase created successfully.'
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
    'title' => 'Create Purchase',
    'subtitle' => 'Create one purchase with multiple products.',
    'submitText' => 'Create Purchase',
    'loadingText' => 'Creating...',
])