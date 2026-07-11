<?php

namespace App\Livewire\Finance\Import;

use App\Services\LedgerService;

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
        } catch (\Throwable $e) {
            $this->status = 'error';
            $this->errors = [$e->getMessage()];
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
