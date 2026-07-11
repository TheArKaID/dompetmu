<?php

use App\Livewire\Finance;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // ── Legacy dashboard ─────────────────────────────────────────────────────
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // ── Finance routes ────────────────────────────────────────────────────────
    Route::get('finance',              Finance\Dashboard::class)->name('finance.dashboard');
    Route::get('finance/transactions', Finance\Transactions\Index::class)->name('finance.transactions');
    Route::get('finance/accounts',     Finance\Accounts\Index::class)->name('finance.accounts');
    Route::get('finance/categories',   Finance\Categories\Index::class)->name('finance.categories');
    Route::get('finance/import',       Finance\Import\Index::class)->name('finance.import');
});

require __DIR__.'/settings.php';
