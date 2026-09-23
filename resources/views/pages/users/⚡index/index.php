<?php

use App\Exports\UserExport;
use App\Imports\UserImport;
use App\Models\City;
use App\Models\Governorate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

new #[Title('User')] class extends Component {
    use WithPagination, WithFileUploads;

    public $importFile;

    public $search = '';

    public $governorate_id = null;

    public $city_id = null;

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
        'governorate_id' => ['except' => null],
        'city_id' => ['except' => null],
        'email' => ['except' => ''],
        'gender' => ['except' => ''],
        'age' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];


    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function users()
    {
        return User::query()
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
                $this->age !== '',
                fn ($q) => $q->where(
                    'age',
                    $this->age
                )
            )

            ->when(
                $this->email,
                fn ($q) => $q->where(
                    'email',
                    $this->email
                )
            )

            ->when(
                $this->gender,
                fn ($q) => $q->where(
                    'gender',
                    $this->gender
                )
            )

            ->with([
                'governorate:id,name',
                'city:id,name',
            ])

            ->orderBy(
                $this->sortField,
                $this->sortDirection
            )

            ->paginate($this->perPage);
    }


    /*
    |--------------------------------------------------------------------------
    | Governorates
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function governorates()
    {
        return Governorate::query()
            ->orderBy('name')
            ->get(['id', 'name']);
    }


    /*
    |--------------------------------------------------------------------------
    | Cities
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function cities()
    {
        if (!$this->governorate_id) {
            return collect();
        }

        return City::query()
            ->where(
                'governorate_id',
                $this->governorate_id
            )
            ->orderBy('name')
            ->get(['id', 'name']);
    }


    /*
    |--------------------------------------------------------------------------
    | Emails
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function emails()
    {
        return User::query()
            ->whereNotNull('email')
            ->distinct()
            ->pluck('email')
            ->sort()
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Ages
    |--------------------------------------------------------------------------
    */

    #[Computed]
    public function ages()
    {
        return User::query()
            ->whereNotNull('age')
            ->distinct()
            ->pluck('age')
            ->sort()
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | Reactive Filters
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }


    public function updatedGovernorateId($value): void
    {
        // المحافظة اتغيرت
        // إذن المدينة القديمة أصبحت غير صالحة
        $this->city_id = null;

        $this->resetPage();
    }


    public function updatedCityId(): void
    {
        $this->resetPage();
    }


    public function updatedEmail(): void
    {
        $this->resetPage();
    }


    public function updatedGender(): void
    {
        $this->resetPage();
    }


    public function updatedAge(): void
    {
        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Reset Filters
    |--------------------------------------------------------------------------
    */

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'governorate_id',
            'city_id',
            'email',
            'gender',
            'age',
        ]);

        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === 'asc'
                    ? 'desc'
                    : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function confirmDelete($userId): void
    {
        $this->UserToDelete = $userId;
        $this->showDeleteModal = true;
    }


    public function deleteUser(): void
    {
        if (!$this->UserToDelete) {
            return;
        }

        User::find($this->UserToDelete)?->delete();

        $this->showDeleteModal = false;

        $this->UserToDelete = null;

        session()->flash(
            'message',
            'User Deleted Successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Bulk Selection
    |--------------------------------------------------------------------------
    */

    public function updateSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->users
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }


    public function bulkDelete(): void
    {
        if (empty($this->selected)) {
            return;
        }

        User::whereIn('id', $this->selected)->delete();

        $this->selected = [];

        $this->selectAll = false;

        session()->flash(
            'message',
            'Selected User Deleted Successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Export PDF
    |--------------------------------------------------------------------------
    */

    public function exportPdf()
    {
        $users = $this->getFilteredUsers();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'dejavusans',
        ]);

        $html = view(
            'users.pdf',
            compact('users')
        )->render();

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            function () use ($mpdf) {
                echo $mpdf->Output('', 'S');
            },
            'users.pdf'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Export Selected PDF
    |--------------------------------------------------------------------------
    */

    public function exportSelected()
    {
        $users = User::whereIn(
            'id',
            $this->selected
        )->get();

        $pdf = Pdf::loadView(
            'users.pdf',
            compact('users')
        );

        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->stream();
            },
            'users-' . date('Y-m-d') . '.pdf'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Export Excel
    |--------------------------------------------------------------------------
    */

    public function exportExcel()
    {
        return Excel::download(
            new UserExport($this->getFilteredUsers()),
            'users-' . date('Y-m-d') . '.xlsx'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Export Selected Excel
    |--------------------------------------------------------------------------
    */

    public function exportSelectedExcel()
    {
        $users = User::whereIn(
            'id',
            $this->selected
        )->get();

        return Excel::download(
            new UserExport($users),
            'users-' . date('Y-m-d') . '.xlsx'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Filtered Users
    |--------------------------------------------------------------------------
    */

    public function getFilteredUsers()
    {
        return User::query()

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
                $this->email,
                fn ($q) => $q->where(
                    'email',
                    $this->email
                )
            )

            ->when(
                $this->gender,
                fn ($q) => $q->where(
                    'gender',
                    $this->gender
                )
            )

            ->when(
                $this->age !== '',
                fn ($q) => $q->where(
                    'age',
                    $this->age
                )
            )

            ->orderBy(
                $this->sortField,
                $this->sortDirection
            )

            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | Import
    |--------------------------------------------------------------------------
    */

    public function import()
    {
        $this->validate([
            'importFile' =>
                'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {

            Excel::import(
                new UserImport(),
                $this->importFile
            );

            session()->flash(
                'message',
                'Users imported successfully.'
            );

            $this->importFile = null;

            return redirect('/users');

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

            Log::warning(
                'User import validation failed',
                ['errors' => $errors]
            );

            session()->flash(
                'error',
                'Some rows could not be imported.'
            );

        } catch (\Throwable $th) {

            Log::error(
                'Import Error',
                [
                    'message' => $th->getMessage(),
                    'file' => $th->getFile(),
                    'line' => $th->getLine(),
                    'trace' => $th->getTraceAsString(),
                ]
            );

            session()->flash(
                'error',
                $th->getMessage()
            );

            return redirect('/users');
        }
    }
};