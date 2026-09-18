<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
  public ?Product $product = null;

  #[Validate('required|min:2')]
  public $name = '';
  /**_______________________**/
  #[Validate('required|unique:products,barcode')]
  public $barcode ='';
 
  /**_______________________**/

  #[Validate('nullable|min:20')]
  public $description = '';
  /**_______________________**/
  #[Validate('required|min:2')]
  public $price = '';
  /**_______________________**/
  #[Validate('required|min:2')]
  public $current_quantity = '';
  /**_______________________ **/
  #[Validate(['required', 'in:active,inactive'])]
  public $status = '';
  /**_______________________**/
  #[Validate(['nullable'])]
  public $image = '';
  /**_______________________**/



  public function setProduct(Product $product)
  {
    $this->product = $product;
    $this->name = $product->name;
    $this->barcode = $product->barcode;
    $this->description = $product->description;
    $this->price = $product->price;
    $this->current_quantity = $product->current_quantity;
    $this->status = $product->status;
    $this->image = $product->image;
 

  }


  public function store()
  {
    $this->validate();
    Product::create($this->only(['name', 'barcode', 'description', 'price', 'current_quantity', 'status', 'image']));
    $this->reset();

  }

  public function update()
  {
    $this->validate(['barcode' => 'required|unique:products,barcode,' . $this->product->id]);
    $this->product->update($this->only(['name', 'barcode', 'description', 'price', 'current_quantity', 'status', 'image']));
    $this->reset();

  }
}
