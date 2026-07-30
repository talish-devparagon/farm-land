<?php

use App\Livewire\Animals\AnimalForm;
use App\Livewire\Animals\AnimalShow;
use App\Livewire\Animals\AnimalsIndex;
use App\Livewire\Dashboard;
use App\Livewire\Upcoming;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');

    Route::livewire('animals', AnimalsIndex::class)->name('animals.index');
    Route::livewire('animals/create', AnimalForm::class)->name('animals.create');
    Route::livewire('animals/{animal}/edit', AnimalForm::class)->name('animals.edit');
    Route::livewire('animals/{animal}', Upcoming::class)->name('animals.show');

    Route::livewire('upcoming', AnimalShow::class)->name('upcoming.index');
});

require __DIR__.'/settings.php';
