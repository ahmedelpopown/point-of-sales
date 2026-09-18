<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $this->createEmployee(
            firstName: 'System',
            lastName: 'Admin',
            email: 'admin@pos.test',
            password: 'password',
            department: 'Administration',
            position: 'System Administrator',
            salary: 0,
            phone: '01000000001',
            address: 'Cairo',
            role: 'super-admin',
        );

        $this->createEmployee(
            firstName: 'Ahmed',
            lastName: 'Manager',
            email: 'manager@pos.test',
            password: 'password',
            department: 'Management',
            position: 'Manager',
            salary: 15000,
            phone: '01000000002',
            address: 'Giza',
            role: 'manager',
        );

        $this->createEmployee(
            firstName: 'Omar',
            lastName: 'Cashier',
            email: 'cashier@pos.test',
            password: 'password',
            department: 'Sales',
            position: 'Cashier',
            salary: 7000,
            phone: '01000000003',
            address: 'Cairo',
            role: 'cashier',
        );

        $this->createEmployee(
            firstName: 'Mahmoud',
            lastName: 'Inventory',
            email: 'inventory@pos.test',
            password: 'password',
            department: 'Inventory',
            position: 'Inventory Manager',
            salary: 10000,
            phone: '01000000004',
            address: 'Giza',
            role: 'inventory-manager',
        );

        $this->createEmployee(
            firstName: 'Youssef',
            lastName: 'Accountant',
            email: 'accountant@pos.test',
            password: 'password',
            department: 'Finance',
            position: 'Accountant',
            salary: 11000,
            phone: '01000000005',
            address: 'Cairo',
            role: 'accountant',
        );

        /*
        |--------------------------------------------------------------------------
        | Extra Employees
        |--------------------------------------------------------------------------
        */

        Employee::factory(20)->create()->each(function (Employee $employee) {
            $employee->assignRole('cashier');
        });
    }

    private function createEmployee(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $department,
        string $position,
        float|int $salary,
        string $phone,
        string $address,
        string $role,
    ): Employee {
        $employee = Employee::updateOrCreate(
            [
                'email' => $email,
            ],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => Hash::make($password),
                'department' => $department,
                'position' => $position,
                'salary' => $salary,
                'phone' => $phone,
                'hire_date' => now(),
                'status' => 'active',
                'address' => $address,
            ]
        );

        $employee->syncRoles([$role]);

        return $employee;
    }
}