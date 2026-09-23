<?php

namespace App\Livewire\Forms;

use App\Models\Employee;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmployeeForm extends Form
{
    public ?Employee $employee = null;

    #[Validate('required|min:2')]
    public string $first_name = '';

    #[Validate('required|min:2')]
    public string $last_name = '';

    public string $email = '';

    #[Validate('required')]
    public string $department = '';

    #[Validate('required')]
    public string $position = '';

    #[Validate('required|numeric|min:2')]
    public $salary = '';

    #[Validate('required')]
    public string $phone = '';

    #[Validate('required|date')]
    public $hire_date = '';

    #[Validate('required|in:active,inactive')]
    public string $status = 'active';

    #[Validate('required')]
    public string $address = '';

    public string $password = '';

    public $shop_id = null;

    public $warehouse_id = null;


    /*
    |--------------------------------------------------------------------------
    | Set Employee
    |--------------------------------------------------------------------------
    */

    public function setEmployee(Employee $employee): void
    {
        $this->employee = $employee;

        $this->first_name = $employee->first_name;
        $this->last_name = $employee->last_name;
        $this->email = $employee->email;
        $this->department = $employee->department;
        $this->position = $employee->position;

        // Never load the password hash into the form.
        $this->password = '';

        $this->salary = $employee->salary;
        $this->phone = $employee->phone;

        $this->hire_date = $employee->hire_date
            ? $employee->hire_date->format('Y-m-d')
            : '';

        $this->status = $employee->status;
        $this->address = $employee->address;

        $this->shop_id = $employee->shop_id;
        $this->warehouse_id = $employee->warehouse_id;
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(): void
    {
        $this->validate($this->storeRules());

        Employee::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'department' => $this->department,
            'position' => $this->position,
            'salary' => $this->salary,
            'phone' => $this->phone,
            'hire_date' => $this->hire_date,
            'status' => $this->status,
            'address' => $this->address,
            'password' => $this->password,
            'shop_id' => $this->shop_id,
            'warehouse_id' => $this->warehouse_id,
        ]);

        $this->reset();
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

public function update(): Employee
{
    if (!$this->employee) {
        throw new \LogicException(
            'Employee must be loaded before update.'
        );
    }

    $this->validate($this->updateRules());

    $data = [
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'email' => $this->email,
        'department' => $this->department,
        'position' => $this->position,
        'salary' => $this->salary,
        'phone' => $this->phone,
        'hire_date' => $this->hire_date,
        'status' => $this->status,
        'address' => $this->address,
        'shop_id' => $this->shop_id,
        'warehouse_id' => $this->warehouse_id,
    ];

    if (filled($this->password)) {
        $data['password'] = $this->password;
    }

    $this->employee->update($data);

    return $this->employee->refresh();
}

    /*
    |--------------------------------------------------------------------------
    | Store Rules
    |--------------------------------------------------------------------------
    */

    protected function storeRules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],

            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:employees,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'department' => [
                'required',
                Rule::in([
                    'sales',
                    'inventory',
                    'purchasing',
                    'supplier',
                ]),
            ],

            'position' => [
                'required',
                'string',
                'max:255',
            ],

            'salary' => [
                'required',
                'numeric',
                'min:2',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'hire_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'address' => [
                'required',
                'string',
                'max:500',
            ],

            'shop_id' => [
                'nullable',
                'required_if:department,sales',
                'exists:shops,id',
            ],

            'warehouse_id' => [
                'nullable',
                'required_if:department,inventory',
                'exists:warehouses,id',
            ],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Update Rules
    |--------------------------------------------------------------------------
    */

 protected function updateRules(): array
{
    return [
        'first_name' => [
            'required',
            'string',
            'min:2',
            'max:255',
        ],

        'last_name' => [
            'required',
            'string',
            'min:2',
            'max:255',
        ],

        'email' => [
            'required',
            'email',
            'max:255',
            \Illuminate\Validation\Rule::unique('employees', 'email')
                ->ignore($this->employee->id),
        ],

        'password' => [
            'nullable',
            'string',
            'min:8',
        ],

        'department' => [
            'required',
            \Illuminate\Validation\Rule::in([
                'sales',
                'inventory',
                'purchasing',
                'supplier',
            ]),
        ],

        'position' => [
            'required',
            'string',
            'max:255',
        ],

        'salary' => [
            'required',
            'numeric',
            'min:2',
        ],

        'phone' => [
            'required',
            'string',
            'max:30',
        ],

        'hire_date' => [
            'required',
            'date',
        ],

        'status' => [
            'required',
            \Illuminate\Validation\Rule::in([
                'active',
                'inactive',
            ]),
        ],

        'address' => [
            'required',
            'string',
            'max:500',
        ],

        'shop_id' => [
            'nullable',
            'required_if:department,sales',
            'exists:shops,id',
        ],

        'warehouse_id' => [
            'nullable',
            'required_if:department,inventory',
            'exists:warehouses,id',
        ],
    ];
}
}