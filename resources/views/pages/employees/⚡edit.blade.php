<?php

use App\Livewire\Concerns\HasEmployeeFormData;
use App\Livewire\Forms\EmployeeForm;
use App\Models\Employee;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

new #[Title('Edit Employee')] class extends Component
{
    use HasEmployeeFormData;

    public EmployeeForm $form;

    public string $role = '';

    public function mount(Employee $employee): void
    {
        $this->form->setEmployee($employee);

        $this->role = $employee->roles
            ->first()?->name ?? '';
    }

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

    $employee = $this->form->update();

    $employee->syncRoles([$role]);

    session()->flash(
        'message',
        'Employee updated successfully.'
    );

    $this->redirectRoute('employees.index');
}
};
?>

@include('pages.employees.form', [
    'title' => 'Edit Employee',
    'subtitle' => 'Update employee personal, employment, location, and role information.',
    'submitText' => 'Update Employee',
    'loadingText' => 'Updating...',
    'isEdit' => true,
])