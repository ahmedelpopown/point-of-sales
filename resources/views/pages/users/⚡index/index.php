<?php

use App\Exports\UserExport;
use App\Imports\UserImport;
use App\Models\City;
use App\Models\Governorate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Validators\ValidationException;


 
new #[Title('User')] class extends Component {
    use WithPagination, WithFileUploads;
    public $importFile;
    public $search = '';

    public $governorate_id;
    public $governorate;
    public $city_id;
    public $city;
    public $gender = '';
    public $email = '';
    public $age = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public $perPage = 10;

    public $selected = [];
    public $selectAll = false;


    public $showDeleteModal = false;
    public $UserToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'governorates' => ['except' => ''],
        'email' => ['except' => ''],
        'gender' => ['except' => ''],
        'city' => ['except' => ''],
        'age' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];


    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when(
                $this->governorate_id,
                fn($q) =>
                $q->where('governorate_id', $this->governorate_id)
            )
            ->when(
                $this->city_id,
                fn($q) =>
                $q->where('city_id', $this->city_id)
            )

            ->when($this->age, fn($q) => $q->where('age', $this->ages))
            ->when($this->email, fn($q) => $q->where('email', $this->email))
            ->when($this->gender, fn($q) => $q->where('gender', $this->gender))

            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }


    #[Computed]
    public function governorates()
    {
        return Governorate::orderBy('name')->get();
    }
    #[Computed]
    public function cities()
    {
        if (!$this->governorate_id) {
            return collect(); // فاضي لحد ما يختار
        }

        return City::where('governorate_id', $this->governorate_id)
            ->orderBy('name')
            ->get();
    }
    public function emails()
    {
        return User::distinct('email')->pluck('email')->sort();
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


    #[Computed]
    public function ages()
    {
        return User::distinct('age')->pluck('age')->sort();
    }

    public function updateSearch()
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
    public function updateGender()
    {
        $this->resetPage();
    }
    public function updateCities()
    {
        $this->resetPage();
    }
    public function updateAge()
    {
        $this->resetPage();
    }
    public function resetFilters()
    {
        $this->reset(['search', 'governorate_id', 'email', 'gender', 'city_id', 'age']);
        $this->resetPage();
    }



    public function confirmDelete($userId)
    {
        $this->UserToDelete = $userId;
        $this->showDeleteModal = true;

    }

    public function deleteUser()
    {
        if ($this->UserToDelete) {
            User::find($this->UserToDelete)->delete();
            $this->showDeleteModal = false;
            $this->UserToDelete = null;
            session()->flash('message', 'User Deleted Successfully.');
        }

    }

    public function updateSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->users->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }


    public function bulkDelete()
    {
        User::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        session()->flash('message', 'Selected User Deleted Successfully.');
    }

 

    public function exportPdf()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->governorate_id, fn($q) => $q->where('governorate_id', $this->governorate_id))
            ->when($this->email, fn($q) => $q->where('email', $this->email))
            ->when($this->gender, fn($q) => $q->where('gender', $this->gender))
            ->when($this->city_id, fn($q) => $q->where('city_id', $this->city_id))
            ->when($this->age, fn($q) => $q->where('age', $this->age))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();


        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'dejavusans'
        ]);

        $html = view('users.pdf', compact('users'))->render();

        $mpdf->WriteHTML($html);

        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'users.pdf');


    }



    public function exportSelected()
    {
        $users = User::whereIn('id', $this->selected)->get();
        $pdf = Pdf::loadView('users.pdf', compact('users'));
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'users-' . date('Y-m-d') . '.pdf');
    }


    public function exportExcel()
    {
        return Excel::download(new UserExport($this->getFilteredUsers()), 'users-' . date('Y-m-d') . '.xlsx');
    }


    public function exportSelectedExcel()
    {
        $users = User::whereIn('id', $this->selected)->get();
        return Excel::download(new UserExport($users), 'users-' . date('Y-m-d') . '.xlsx');
    }


    public function getFilteredUsers()
    {
        return User::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->governorate_id, fn($q) => $q->where('governorate_id', $this->governorate_id))
            ->when($this->email, fn($q) => $q->where('email', $this->email))
            ->when($this->gender, fn($q) => $q->where('gender', $this->gender))
            ->when($this->city_id, fn($q) => $q->where('city', $this->city_id))
            ->when($this->age, fn($q) => $q->where('age', $this->age))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
    }

 

public function import()
{
    $this->validate([
        'importFile' => 'required|file|mimes:xlsx,xls,csv|max:2048'
    ]);

    try {

        Excel::import(new UserImport(), $this->importFile);

        session()->flash('message', 'Users imported successfully.');
        $this->importFile = null;

        return redirect('/users');

    } catch (ValidationException $e) {

        $failures = $e->failures();

        $errors = [];

        foreach ($failures as $failure) {

            $errors[] = [
                'row'       => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors'    => $failure->errors(),
                'values'    => $failure->values(),
            ];
        }

       

    } catch (\Throwable $th) {

        Log::error('Import Error', [
            'message' => $th->getMessage(),
            'file'    => $th->getFile(),
            'line'    => $th->getLine(),
            'trace'   => $th->getTraceAsString(),
        ]);

        session()->flash('error', $th->getMessage());

        return redirect('/users');
    }
}

};