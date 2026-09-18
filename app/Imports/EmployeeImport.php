<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeeImport implements ToModel, WithHeadingRow, WithValidation
{

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Employee([
            'email' => $row['email'],
            'password' => $row['password'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'phone' => $row['phone'] ?? null,
            'department' => $row['department'],
            'position' => $row['position'],
            'salary' => $row['salary'],
            'hire_date' => $row['hire_date'],
            'address' => $row['address'],
            'status' => $row['status'] ?? 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:employees,email',
            'password' => 'nullable',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'nullable|string',
            'department' => 'nullable|string',
            'position' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'hire_date' => 'nullable|date',
            'address' => 'nullable',
            'status' => 'nullable',         
        ];
    }
}
