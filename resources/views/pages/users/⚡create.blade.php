<?php

use App\Livewire\Forms\UserForm;
use App\Models\City;
use App\Models\Governorate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Create User')] class extends Component
{
    public UserForm $form;

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
            $this->form->city_id = '';
        }
    }

    public function save()
    {
        $this->form->store();

        session()->flash('message', 'User created successfully.');

        return $this->redirectRoute('users.index');
    }
};
?>

@include('pages.users.form', [
    'title' => 'Create User',
    'subtitle' => 'Create a new user and assign their information.',
    'submitText' => 'Create User',
    'loadingText' => 'Creating...',
    'isEdit' => false,
])
