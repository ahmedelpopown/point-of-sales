<?php

use App\Livewire\Concerns\HasEmployeeFormData;
use App\Livewire\Forms\EmployeeForm;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Title('Create Employee')] class extends Component
{
    use HasEmployeeFormData;

    public EmployeeForm $form;

    public string $role = '';

    public function save(): void
    {
        $this->validate([
            'role' => [
                'required',
                'string',
            ],
        ]);

        $role = Role::query()
            ->where('name', $this->role)
            ->where('guard_name', 'employee')
            ->first();

        if (!$role) {
            $this->addError(
                'role',
                'The selected role is invalid.'
            );

            return;
        }

        $employee = $this->form->store();

        $employee->syncRoles([$role]);

        session()->flash(
            'message',
            'Employee created successfully.'
        );

        $this->redirectRoute('employees.index');
    }
};
?>

@include('pages.employees.form', [
    'title' => 'Create Employee',
    'subtitle' => 'Create a new employee and add their employment information.',
    'submitText' => 'Create Employee',
    'loadingText' => 'Creating...',
    'isEdit' => false,
])