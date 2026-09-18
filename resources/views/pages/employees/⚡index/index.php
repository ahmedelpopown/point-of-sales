<?php
use App\Exports\EmployeeExport;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\PDF;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;



new #[Title('Employees')] class extends Component {
    use WithPagination, WithFileUploads;
    public $importFile;
    public $search = '';
    public $department = '';
    public $status = '';
    public $sortField = 'first_name';
    public $sortDirection = 'asc';

    public $perPage = 10;

    public $selected = [];
    public $selectAll = false;


    public $showDeleteModal = false;
    public $EmployeeToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'department' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'first_name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    /* Filters */
    #[Computed]
    public function employees()
    {
        return Employee::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->department, fn($q) => $q->where('department', $this->department))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function departments()
    {
        return Employee::distinct('department')->pluck('department')->sort();
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

    /* Reset Filters */
    public function updateSearch()
    {
        $this->resetPage();
    }
    public function updateDepartment()
    {
        $this->resetPage();
    }
    public function updateStatus()
    {
        $this->resetPage();
    }
    public function resetFilters()
    {
        $this->reset(['search', 'department', 'status']);
        $this->resetPage();
    }



    /*Action Delete & Select */
    public function confirmDelete($employeeId)
    {
        $this->EmployeeToDelete = $employeeId;
        $this->showDeleteModal = true;

    }

    public function deleteEmployee()
    {
        if ($this->EmployeeToDelete) {
            Employee::find($this->EmployeeToDelete)->delete();
            $this->showDeleteModal = false;
            $this->EmployeeToDelete = null;
            session()->flash('message', 'Employee deleted successfully.');
        }

    }

    public function updateSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->employees->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function bulkDelete()
    {
        Employee::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        session()->flash('message', 'Selected Employees Deleted Successfully.');
    }







    /*Exports */
    public function exportPdf()
    {
        $employees = Employee::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->department, fn($q) => $q->where('department', $this->department))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
        $pdf = Pdf::loadView('employees.pdf', compact('employees'));
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'employees-' . date('Y-m-d') . '.pdf');
    }

    public function exportSelected()
    {
        $employees = Employee::whereIn('id', $this->selected)->get();
        $pdf = Pdf::loadView('employees.pdf', compact('employees'));
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'employees-' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        return Excel::download(new EmployeeExport($this->getFilteredEmployees()), 'employees-' . date('Y-m-d') . '.xlsx');
    }
    public function exportSelectedExcel()
    {
        $employees = Employee::whereIn('id', $this->selected)->get();
        return Excel::download(new EmployeeExport($employees), 'employees-' . date('Y-m-d') . '.xlsx');
    }
    public function getFilteredEmployees()
    {
        return Employee::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->department, fn($q) => $q->where('department', $this->department))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
    }


    public function import()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);
        // 


        try {
            Excel::import(new EmployeeImport(), $this->importFile);
            session()->flash('message', 'Employees imported successfully.');
            $this->importFile = null;
            return redirect('/employees');
        } catch (\Throwable $th) {
            session()->flash('error', 'There was an error importing the file: ' . $th->getMessage());
            return $this->redirect('/employees');
        }
    }

};