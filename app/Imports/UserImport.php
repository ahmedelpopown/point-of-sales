<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => $row['password'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'age' => $row['age'],
            'gender' => $row['gender'],
            'city_id' => $row['city_id'],
            'governorate_id' => $row['governorate_id'],
        ]);
    }

    public function rules(): array
    {
        return [

            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'age' => 'required',
            'gender' => 'required',
            'city_id' => 'required',
            'governorate_id' => 'required',

        ];
    }
}
