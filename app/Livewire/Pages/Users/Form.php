<?php

namespace App\Livewire\Pages\Users;

use App\Livewire\Forms\UserForm;
use App\Models\City;
use App\Models\Governorate;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Form extends Component
{
    public UserForm $form;

    public ?User $user = null;

    public function mount(?User $user = null): void
    {
        $this->user = $user;

        if ($user) {
            $this->form->setUser($user);
        }
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

    public function updatedFormGovernorateId(): void
    {
        $this->form->city_id = null;
    }

    public function save()
    {
        if ($this->user) {
            $this->form->update();
            session()->flash('message', 'User updated successfully.');
        } else {
            $this->form->store();
            session()->flash('message', 'User created successfully.');
        }

        return $this->redirectRoute('users.index');
    }

    public function render()
    {
        return view('pages.users.form',[
             'title' => 'Create User',
    'subtitle' => 'Create a new user and assign their information.',
    'submitText' => 'Create User',
    'loadingText' => 'Creating...',
    'isEdit' => false,
        ]);
    }
}