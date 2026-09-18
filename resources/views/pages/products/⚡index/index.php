<?php

use App\Exports\ProductsExport;
use App\Imports\ProductImport;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;


new #[Title('Products')] class extends Component {
     use WithPagination, WithFileUploads;
     public $importFile;
     public $search = '';
     public $name = '';
     public $price = '';
     public $barcode = '';
     public $status = '';
     public $current_quantity = '';
     public $sortField = 'name';
     public $sortDirection = 'asc';
     public $perPage = 10;
     public $showDeleteModal = false;
     public $ProductToDelete = null;
     public $selected = [];
     public $selectAll = false;




     protected $queryString = [
          'search' => ['except' => ''],
          'name' => ['except' => ''],
          'barcode' => ['except' => ''],
          'price' => ['except' => ''],
          'current_quantity' => ['except' => ''],
          'status' => ['except' => ''],
          'sortField' => ['except' => 'name'],
          'sortDirection' => ['except' => 'asc'],
     ];

     #[Computed]
     public function products()
     {
          return Product::query()
               ->when($this->search, fn($q) => $q->search($this->search))
               ->when($this->name, fn($q) => $q->where('name', $this->name))
               ->withSum('stocks as current_quantity', 'quantity')
               ->when($this->barcode, fn($q) => $q->where('barcode', $this->barcode))
               ->when($this->status, fn($q) => $q->where('status', $this->status))
               ->orderBy($this->sortField, $this->sortDirection)
               ->paginate($this->perPage);
     }

     


  
     #[Computed]
     public function status()
     {
          return Product::distinct('status')->pluck('status')->sort();
     }
     #[Computed]
     public function currentQuantity()
     {
          return Product::distinct('current_quantity')->pluck('current_quantity')->sort();
     }

     public function sortBy($filed)
     {
          if ($this->sortField === $filed) {
               $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
          } else {
               $this->sortField = $filed;
               $this->sortDirection = 'asc';
          }
          $this->resetPage();
     }




     public function updateSearch()
     {
          $this->resetPage();
     }
     public function updateCurrentQuantity()
     {
          $this->resetPage();
     }

     public function updateStatus()
     {
          $this->resetPage();
     }
     public function resetFilters()
     {
          $this->reset(['search', 'status','currentQuantity']);
          $this->resetPage();
     }



     public function confirmDelete($productId)
     {
          $this->ProductToDelete = $productId;
          $this->showDeleteModal = true;

     }

         public function deleteProduct()
    {
        if ($this->ProductToDelete) {
            Product::find($this->ProductToDelete)->delete();
            $this->showDeleteModal = false;
            $this->ProductToDelete = null;
            session()->flash('message', 'Product deleted successfully.');
        }

    }


        public function updateSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->products->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }


      public function bulkDelete()
    {
        Product::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        session()->flash('message', 'Selected Products Deleted Successfully.');
    }





        public function exportPdf()
    {
        $products = Product::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->price, fn($q) => $q->where('price', $this->price))
            ->withSum('stocks as current_quantity', 'quantity')
            ->when($this->barcode , fn($q) =>  $q->where('barcode', $this->barcode ))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
   
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'dejavusans'
        ]);

        $html = view('products.pdf', compact('products'))->render();

        $mpdf->WriteHTML($html);

        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'products.pdf');
    }


        public function exportSelected()
    {
        $products = Product::whereIn('id', $this->selected)->get();
          $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'dejavusans'
        ]);

        $html = view('products.pdf', compact('products'))->render();

        $mpdf->WriteHTML($html);

        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'products.pdf');

    }



        public function getFilteredProducts()
    {
        return Product::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->barcode, fn($q) => $q->where('barcode', $this->barcode))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
    }



        public function exportExcel()
    {
        return Excel::download(new ProductsExport($this->getFilteredProducts()), 'products-' . date('Y-m-d') . '.xlsx');
    }


    public function exportSelectedExcel()
    {
        $products = Product::whereIn('id', $this->selected)->get();
        return Excel::download(new ProductsExport($products), 'products-' . date('Y-m-d') . '.xlsx');
    }





    
     public function import()
     {
          $this->validate([
               'importFile' => 'required|file|mimes:xlsx,xls,csv|max:2048'
          ]);
          try {
               Excel::import(new ProductImport(), $this->importFile);
               session()->flash('message', 'products imported successfully.');
               $this->importFile = null;
               redirect('/products');

          } catch (\Throwable $th) {
               session()->flash('error', 'There was an error importing the file: ' . $th->getMessage());
               redirect('/products');
          }
     }

};