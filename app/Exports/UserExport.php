<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection,WithHeadings,WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
   protected $users; 
    public function __construct( $users=null)
    {
        $this->users = $users;

    }
    public function collection()
    {
        return $this->users ??User::all()   ;
    }

    public function headings(): array
    {
        return [
            
            'name',
            'email',
            'phone',
            'governorate',
            'city ',       
            'age',
            'address ',       
            'gender',
            ];
            }
            public function map($user): array
            {
                return [
                 
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->governorate->name,
                    $user->city->name,
                    $user->age,
                    $user->address,
                    $user->gender,
          
                ];
            }



}
