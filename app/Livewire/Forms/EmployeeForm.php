<?php

namespace App\Livewire\Forms;

use App\Models\Employee;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmployeeForm extends Form
{
    
    public ?Employee $employee = null;

    #[Validate('required|min:2')]
    public $first_name='';

    
    #[Validate('required|min:2')]
    public $last_name='';
    
    #[Validate('required|email|unique:employees,email')]
    public $email='';

    
    #[Validate('required')]
    public $department='';

    
    #[Validate('required')]
    public $position='';

    #[Validate('required|numeric|min:2')]
    public $salary='';

    #[Validate('required')]
    public $phone='';

    #[Validate('required|date')]
    public $hire_date='';

    #[Validate('required|in:active,inactive')]
    public $status='active';

    #[Validate('required')]
    public $address='';

    #[Validate('required|min:8')]
    public $password='';

    #[Validate('nullable|exists:shops,id')]
    public $shop_id = '';

    public function setEmployee(Employee $employee)
    {
        $this->employee = $employee;
        $this->first_name = $employee->first_name;
        $this->last_name = $employee->last_name;
        $this->email = $employee->email;
        $this->department = $employee->department;
        $this->position = $employee->position;
        $this->password = $employee->password;
        $this->salary = $employee->salary;
        $this->phone = $employee->phone;
        $this->hire_date = $employee->hire_date->format('Y-m-d');
        $this->status = $employee->status;
        $this->address = $employee->address;
        $this->shop_id = $employee->shop_id;
    }

    public function store()
    {
        $this->validate();
        Employee::create($this->only(['first_name', 'last_name', 'email', 'department', 'position', 'salary', 'phone', 'hire_date', 'status', 'address', 'password', 'shop_id']));
        $this->reset();
    }

    public function update()
    {
        $this->validate(['email' => 'required|email|unique:employees,email,' . $this->employee->id]);
        $this->employee->update($this->only(['first_name', 'last_name', 'email', 'department', 'position', 'salary', 'phone', 'hire_date', 'status', 'address', 'password', 'shop_id']));
        $this->reset();
    }

}