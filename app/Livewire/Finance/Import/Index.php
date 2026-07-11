<?php

namespace App\Livewire\Finance\Import;

use App\Services\LedgerService;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Import Data — FinArka')]
class Index extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:csv,txt|max:10240')]
    public $csvFile = null;

    public string $status      = 'idle';   // idle | done | error
    public int $imported       = 0;
    public int $total          = 0;
    public array $errors       = [];

    public function import(LedgerService $service): void
    {
        $this->validate();

        $this->status   = 'idle';
        $this->errors   = [];
        $this->imported = 0;

        try {
            $result = $service->importFromCsv($this->csvFile);

            $this->imported = $result['imported'];
            $this->total    = $result['total'];
            $this->errors   = $result['errors'];
            $this->status   = 'done';

            Flux::toast(
                heading: 'Import Selesai',
                text: "{$this->imported} dari {$this->total} transaksi berhasil diimpor.",
                variant: 'success'
            );
        } catch (\Throwable $e) {
            $this->status = 'error';
            $this->errors = [$e->getMessage()];
            Flux::toast(heading: 'Import Gagal', text: $e->getMessage(), variant: 'danger');
        }
    }

    public function resetImport(): void
    {
        $this->csvFile  = null;
        $this->status   = 'idle';
        $this->errors   = [];
        $this->imported = 0;
        $this->total    = 0;
    }

    public function render()
    {
        return view('livewire.finance.import.index');
    }
}
