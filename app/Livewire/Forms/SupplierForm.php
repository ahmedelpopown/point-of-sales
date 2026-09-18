<?php

namespace App\Livewire\Forms;

use App\Models\Supplier;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SupplierForm extends Form
{
    public ?Supplier $supplier=null;
    
  #[Validate('required|min:2')]
  public $name = '';
  /**_______________________**/
  #[Validate('required|email|unique:suppliers,email')]
  public $email = '';
  /**_______________________**/

  #[Validate('nullable|min:20')]
  public $address = '';
  /**_______________________**/
  #[Validate('required|min:11')]
  public $phone = '';
  #[Validate('required|exists:cities,id')]
  public $city_id = '';
  /**_______________________**/
  #[Validate('required|exists:governorates,id')]
  public $governorate_id = '';



    public function setSupplier(Supplier $supplier)
  {
    $this->supplier = $supplier;
    $this->name = $supplier->name;
    $this->email = $supplier->email;
    $this->address = $supplier->address;
    $this->phone = $supplier->phone;
    $this->city_id = $supplier->city_id;
    $this->governorate_id = $supplier->governorate_id;

  }



    public function store()
  {
    $this->validate();
    Supplier::create($this->only(['name', 'email', 'address', 'phone',  'city_id', 'governorate_id']));
    $this->reset();

  }


    public function update()
  {
    $this->validate(['email' => 'required|email|unique:suppliers,email,' . $this->supplier->id]);
    $this->supplier->update($this->only(['name', 'email',  'address', 'phone', 'city_id', 'governorate_id']));
    $this->reset();

  }









}
