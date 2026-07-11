<?php

use App\Livewire\Finance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// PIN verification endpoint
Route::post('/pin/verify', function (Request $request) {
    $user = auth()->user();
    if (!$user || !$user->pin) {
        return response()->json(['success' => false]);
    }
    if (Hash::check($request->input('pin'), $user->pin)) {
        session(['pin_unlocked' => true]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false]);
})->middleware(['auth'])->name('pin.verify');

// Redirect root to transactions if authenticated, else welcome
Route::redirect('/', '/finance/transactions')->name('home');
Route::redirect('/dashboard', '/finance/transactions')->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // ── Finance routes ────────────────────────────────────────────────────────
    Route::get('finance',                   fn() => redirect()->route('finance.transactions'))->name('finance.home');
    Route::get('finance/transactions',      Finance\Transactions\Index::class)->name('finance.transactions');
    Route::get('finance/reports',           Finance\Reports\Index::class)->name('finance.reports');
    Route::get('finance/hutang',            Finance\Hutang\Index::class)->name('finance.hutang');
    Route::get('finance/settings',          Finance\Settings\Index::class)->name('finance.settings');

    // ── Legacy sub-pages (now embedded in Settings) ───────────────────────────
    Route::get('finance/accounts',          Finance\Accounts\Index::class)->name('finance.accounts');
    Route::get('finance/categories',        Finance\Categories\Index::class)->name('finance.categories');
    Route::get('finance/import',            Finance\Import\Index::class)->name('finance.import');
});

require __DIR__.'/settings.php';
