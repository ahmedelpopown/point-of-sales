<?php

namespace App\Livewire\Concerns;

trait WithDeleteConfirmation
{
    public bool $showDeleteModal = false;

    public $deleteItemId = null;

    public function confirmDelete($id): void
    {
        $this->deleteItemId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteItem(): void
    {
        if (!$this->deleteItemId) {
            return;
        }

        $this->deleteModel()::find($this->deleteItemId)?->delete();

        $this->showDeleteModal = false;
        $this->deleteItemId = null;

        session()->flash(
            'message',
            $this->deleteSuccessMessage()
        );
    }

    protected function deleteSuccessMessage(): string
    {
        return 'Deleted successfully.';
    }

    abstract protected function deleteModel(): string;
}