<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
  public ?User $user = null;

  #[Validate('required|min:2')]
  public $name = '';
  /**_______________________**/
  #[Validate('required|email|unique:users,email')]
  public $email = '';
  /**_______________________**/

  #[Validate('required|min:6')]
  public $password = '';
  /**_______________________**/

  #[Validate('nullable|min:20')]
  public $address = '';
  /**_______________________**/
  #[Validate('required|min:11')]
  public $phone = '';
  /**_______________________**/
  #[Validate('required|min:2')]
  public $age = '';
  /**_______________________ **/
  #[Validate(['required', 'in:male,female'])]
  public $gender = '';
  /**_______________________**/
  #[Validate('required|exists:cities,id')]
  public $city_id = '';
  /**_______________________**/
  #[Validate('required|exists:governorates,id')]
  public $governorate_id = '';


  public function setUser(User $user)
  {
    $this->user = $user;
    $this->name = $user->name;
    $this->email = $user->email;
    $this->password = $user->password;
    $this->address = $user->address;
    $this->phone = $user->phone;
    $this->age = $user->age;
    $this->gender = $user->gender;
    $this->city_id = $user->city_id;
    $this->governorate_id = $user->governorate_id;

  }


  public function store()
  {
    $this->validate();
    User::create($this->only(['name', 'email', 'password', 'address', 'phone', 'age', 'gender', 'city_id', 'governorate_id']));
    $this->reset();

  }

  public function update()
  {
    $this->validate(['email' => 'required|email|unique:users,email,' . $this->user->id]);
    $this->user->update($this->only(['name', 'email', 'password', 'address', 'phone', 'age', 'gender', 'city_id', 'governorate_id']));
    $this->reset();

  }
}
