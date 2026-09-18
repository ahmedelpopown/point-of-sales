<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeExport implements FromCollection ,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $employees; 
    public function __construct( $employees=null)
    {
        $this->employees = $employees;
    }
    public function collection()
    {
        return $this->employees ??Employee::all()   ;
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Department',
            'Position',
            'Salary',
            'Hire Date',
            'Statues',
            ];
            }
            public function map($employee): array
            {
                return [
                    $employee->id,
                    $employee->first_name,
                    $employee->last_name,
                    $employee->email,
                    $employee->phone,
                    $employee->department,
                    $employee->position,
                    $employee->salary,
                    $employee->hire_date->format('Y-m-d'),
                    $employee->status,
                ];
            }

}
