<?php

use App\Http\Controllers\Admin\RoomRateController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::get('properties',App\Livewire\Properties\Index::class)->name('properties.index');
Route::get('room-rate/{id}', App\Livewire\Roomrate\Index::class)->name('room_rate.index');
Route::get('room-rate/{id}/create', App\Livewire\Roomrate\Create::class)->name('room_rate.create');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
