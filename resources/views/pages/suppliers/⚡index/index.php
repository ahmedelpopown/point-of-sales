<?php

use App\Exports\SupplierExport;
use App\Imports\SupplierImport;
use App\Livewire\Concerns\WithBulkSelection;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Mpdf\Mpdf;

new #[Title('Supplier')] class extends Component
{
    use WithBulkSelection;
    use WithFileUploads, WithPagination;

    public $importFile;

    public $search = '';

    public $governorate_id;

    public $city_id;

    public $email = '';

    public $name = '';

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public $perPage = 10;

    public $showDeleteModal = false;

    public $SupplierToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'email' => ['except' => ''],
        'name' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    #[Computed]
    public function suppliers()
    {
        return Supplier::query()
            ->with([
                'governorate',
                'city',
            ])
            ->withSum('purchases', 'total_price')
            ->withSum('payments', 'amount')
            ->when(
                $this->search,
                fn ($q) => $q->search($this->search)
            )
            ->when(
                $this->governorate_id,
                fn ($q) => $q->where(
                    'governorate_id',
                    $this->governorate_id
                )
            )
            ->when(
                $this->city_id,
                fn ($q) => $q->where(
                    'city_id',
                    $this->city_id
                )
            )
            ->when(
                $this->name,
                fn ($q) => $q->where('name', $this->name)
            )
            ->when(
                $this->email,
                fn ($q) => $q->where('email', $this->email)
            )
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function getBulkVisibleIds(): array
    {
        return $this->suppliers->getCollection()->pluck('id')->all();
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
        $this->clearBulkSelection();
    }

    public function updated($property): void
    {
        if (in_array($property, [
            'search',
            'governorate_id',
            'city_id',
            'email',
            'name',
            'perPage',
        ], true)) {
            $this->resetPage();
            $this->clearBulkSelection();
        }
    }

    #[Computed]
    public function governorates()
    {
        return Governorate::orderBy('name')->get();
    }

    #[Computed]
    public function cities()
    {
        if (! $this->governorate_id) {
            return collect();
        }

        return City::where('governorate_id', $this->governorate_id)
            ->orderBy('name')
            ->get();
    }

    public function emails()
    {
        return Supplier::distinct('email')->pluck('email')->sort();
    }

    public function name()
    {
        return Supplier::distinct('name')->pluck('name')->sort();
    }

    public function updateSearch()
    {
        $this->resetPage();
    }

    public function updateName()
    {
        $this->resetPage();
    }

    public function updateGovernorate()
    {
        $this->resetPage();
    }

    public function updateEmail()
    {
        $this->resetPage();
    }

    public function updateCities()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'governorate_id', 'email', 'city_id', 'name']);
        $this->resetPage();
        $this->clearBulkSelection();
    }

    public function confirmDelete($supplierId)
    {
        $this->SupplierToDelete = $supplierId;
        $this->showDeleteModal = true;

    }

    public function deleteSupplier()
    {
        if ($this->SupplierToDelete) {
            Supplier::find($this->SupplierToDelete)->delete();
            $this->selected = array_values(array_diff(
                $this->selected,
                [(int) $this->SupplierToDelete]
            ));
            $this->syncBulkSelection();
            $this->resetPage();
            $this->showDeleteModal = false;
            $this->SupplierToDelete = null;
            session()->flash('message', 'Supplier Deleted Successfully.');
        }

    }

    public function bulkDelete()
    {
        $ids = $this->normalizeBulkIds($this->selected);

        if ($ids === []) {
            session()->flash('error', 'Select at least one supplier first.');

            return;
        }

        $count = Supplier::query()->whereIn('id', $ids)->delete();
        $this->clearBulkSelection();
        $this->resetPage();
        session()->flash('message', $count.' selected supplier(s) deleted successfully.');
    }

    public function exportPdf()
    {
        $suppliers = Supplier::query()
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->governorate_id, fn ($q) => $q->where('governorate_id', $this->governorate_id))
            ->when($this->email, fn ($q) => $q->where('email', $this->email))
            ->when($this->name, fn ($q) => $q->where('name', $this->name))
            ->when($this->city_id, fn ($q) => $q->where('city_id', $this->city_id))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'dejavusans',
        ]);

        $html = view('suppliers.pdf', compact('suppliers'))->render();

        $mpdf->WriteHTML($html);

        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'suppliers.pdf');

    }

    public function exportSelected()
    {
        $ids = $this->normalizeBulkIds($this->selected);

        if ($ids === []) {
            session()->flash('error', 'Select at least one supplier to export.');

            return null;
        }

        $suppliers = Supplier::whereIn('id', $ids)->get();
        $pdf = Pdf::loadView('suppliers.pdf', compact('suppliers'));
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'dejavusans',
        ]);

        $html = view('suppliers.pdf', compact('suppliers'))->render();

        $mpdf->WriteHTML($html);

        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'suppliers.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new SupplierExport($this->getFilteredSuppliers()), 'suppliers-'.date('Y-m-d').'.xlsx');
    }

    public function exportSelectedExcel()
    {
        $ids = $this->normalizeBulkIds($this->selected);

        if ($ids === []) {
            session()->flash('error', 'Select at least one supplier to export.');

            return null;
        }

        $suppliers = Supplier::whereIn('id', $ids)->get();

        return Excel::download(new SupplierExport($suppliers), 'suppliers-'.date('Y-m-d').'.xlsx');
    }

    public function getFilteredSuppliers()
    {
        return Supplier::query()
            ->when($this->search, fn ($q) => $q->search($this->search))
            ->when($this->name, fn ($q) => $q->where('city', $this->name))
            ->when($this->email, fn ($q) => $q->where('email', $this->email))
            ->when($this->city_id, fn ($q) => $q->where('city', $this->city_id))
            ->when($this->governorate_id, fn ($q) => $q->where('governorate_id', $this->governorate_id))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {

            Excel::import(new SupplierImport, $this->importFile);

            session()->flash('message', 'Suppliers imported successfully.');
            $this->importFile = null;

            return redirect('/suppliers');

        } catch (ValidationException $e) {

            $failures = $e->failures();

            $errors = [];

            foreach ($failures as $failure) {

                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }

        } catch (Throwable $th) {

            Log::error('Import Error', [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString(),
            ]);

            session()->flash('error', $th->getMessage());

            return redirect('/suppliers');
        }
    }
};
