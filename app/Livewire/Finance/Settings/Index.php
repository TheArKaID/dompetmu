<?php

namespace App\Livewire\Finance\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pengaturan — FinArka')]
class Index extends Component
{
    #[Url]
    public string $section = 'main'; // main | rekening | kategori | pin | import

    // ── PIN fields ────────────────────────────────────────────────────────────
    public string $newPin        = '';
    public string $newPinConfirm = '';
    public string $currentPin    = '';
    public bool $pinEnabled      = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->pinEnabled = ! is_null($user->pin);
    }

    // ── PIN Actions ───────────────────────────────────────────────────────────

    public function savePin(): void
    {
        $this->validate([
            'newPin'        => 'required|digits:4',
            'newPinConfirm' => 'required|same:newPin',
        ], [
            'newPin.digits'        => 'PIN harus 4 digit angka.',
            'newPinConfirm.same'   => 'Konfirmasi PIN tidak cocok.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update(['pin' => Hash::make($this->newPin)]);

        // Unlock the session automatically
        session(['pin_unlocked' => true]);

        $this->pinEnabled   = true;
        $this->newPin       = '';
        $this->newPinConfirm = '';

        $this->dispatch('notify', type: 'success', message: 'PIN berhasil disimpan.');
    }

    public function removePin(): void
    {
        $this->validate([
            'currentPin' => 'required|digits:4',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (! Hash::check($this->currentPin, $user->pin ?? '')) {
            $this->addError('currentPin', 'PIN tidak cocok.');
            return;
        }

        $user->update(['pin' => null]);
        session()->forget('pin_unlocked');

        $this->pinEnabled   = false;
        $this->currentPin   = '';

        $this->dispatch('notify', type: 'warning', message: 'PIN berhasil dihapus.');
    }

    public function setSection(string $s): void
    {
        $this->section = $s;
    }

    public function render()
    {
        return view('livewire.finance.settings.index');
    }
}
