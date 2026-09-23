<?php

use App\Livewire\Forms\UserForm;
use App\Models\City;
use App\Models\Governorate;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit User')] class extends Component
{
    public UserForm $form;

    public function mount(User $user): void
    {
        $this->form->setUser($user);
    }

    #[Computed]
    public function governorates()
    {
        return Governorate::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function cities()
    {
        return $this->form->governorate_id
            ? City::query()
                ->where('governorate_id', $this->form->governorate_id)
                ->orderBy('name')
                ->get(['id', 'name'])
            : collect();
    }

    public function updated($property): void
    {
        if ($property === 'form.governorate_id') {
            $this->form->city_id = null;
        }
    }

    public function save()
    {
        $this->form->update();

        session()->flash('message', 'User updated successfully.');

        return $this->redirectRoute('users.index');
    }
};

 
?>
@include('pages.users.form', [
    'title' => 'Edit User',
    'subtitle' => 'Update the user account and personal information.',
    'submitText' => 'Update User',
    'loadingText' => 'Updating...',
    'isEdit' => true,
])