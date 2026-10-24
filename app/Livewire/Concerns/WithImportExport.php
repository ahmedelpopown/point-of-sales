<?php

namespace App\Livewire\Concerns;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

trait WithImportExport
{
    public $importFile;

    /*
    |--------------------------------------------------------------------------
    | Export PDF
    |--------------------------------------------------------------------------
    */

    public function exportPdf()
    {
        $items = $this->filteredQuery()->get();

        $pdf = Pdf::loadView(
            $this->pdfView(),
            [
                $this->resourceKey() => $items,
            ]
        );

        return response()->streamDownload(
            fn () => print ($pdf->stream()),
            $this->fileName('pdf')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Export Selected PDF
    |--------------------------------------------------------------------------
    */

    public function exportSelected()
    {
        $ids = $this->selectedIds();

        if ($ids === []) {
            session()->flash('error', 'Select at least one item to export.');

            return null;
        }

        $items = $this->modelClass()::whereIn(
            'id',
            $ids
        )->get();

        $pdf = Pdf::loadView(
            $this->pdfView(),
            [
                $this->resourceKey() => $items,
            ]
        );

        return response()->streamDownload(
            fn () => print ($pdf->stream()),
            $this->fileName('pdf')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Export Excel
    |--------------------------------------------------------------------------
    */

    public function exportExcel()
    {
        $items = $this->filteredQuery()->get();

        $exportClass = $this->exportClass();

        return Excel::download(
            new $exportClass($items),
            $this->fileName('xlsx')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Export Selected Excel
    |--------------------------------------------------------------------------
    */

    public function exportSelectedExcel()
    {
        $ids = $this->selectedIds();

        if ($ids === []) {
            session()->flash('error', 'Select at least one item to export.');

            return null;
        }

        $items = $this->modelClass()::whereIn(
            'id',
            $ids
        )->get();

        $exportClass = $this->exportClass();

        return Excel::download(
            new $exportClass($items),
            $this->fileName('xlsx')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Import
    |--------------------------------------------------------------------------
    */

    public function import()
    {
        $this->validate([
            'importFile' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:2048',
            ],
        ]);

        try {

            $importClass = $this->importClass();

            Excel::import(
                new $importClass,
                $this->importFile
            );

            $this->importFile = null;

            session()->flash(
                'message',
                $this->importSuccessMessage()
            );

            return $this->redirectRoute(
                $this->indexRoute()
            );

        } catch (\Throwable $e) {

            session()->flash(
                'error',
                'There was an error importing the file: '
                .$e->getMessage()
            );

            return $this->redirectRoute(
                $this->indexRoute()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | File Name
    |--------------------------------------------------------------------------
    */

    protected function fileName(string $extension): string
    {
        return $this->resourceName()
            .'-'.date('Y-m-d')
            .'.'.$extension;
    }

    private function selectedIds(): array
    {
        return array_values(array_unique(array_filter(
            array_map('intval', $this->selected),
            static fn (int $id): bool => $id > 0
        )));
    }

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    */

    abstract protected function modelClass(): string;

    abstract protected function exportClass(): string;

    abstract protected function importClass(): string;

    abstract protected function pdfView(): string;

    abstract protected function resourceName(): string;

    abstract protected function resourceKey(): string;

    abstract protected function indexRoute(): string;

    protected function importSuccessMessage(): string
    {
        return ucfirst($this->resourceName())
            .' imported successfully.';
    }
}
