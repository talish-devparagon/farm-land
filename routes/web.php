<?php

use App\Livewire\Animals\AnimalForm;
use App\Livewire\Animals\AnimalShow;
use App\Livewire\Animals\AnimalsIndex;
use App\Livewire\BreedingRecords\BreedingRecordsIndex;
use App\Livewire\Dashboard;
use App\Livewire\Farm\FarmSettings;
use App\Livewire\HealthRecords\HealthRecordsIndex;
use App\Livewire\Reports\ReportsIndex;
use App\Livewire\Upcoming;
use App\Livewire\WeightLogs\WeightLogsIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');

    Route::livewire('animals', AnimalsIndex::class)->name('animals.index');
    Route::livewire('animals/create', AnimalForm::class)->name('animals.create');
    Route::livewire('animals/{animal}/edit', AnimalForm::class)->name('animals.edit');
    Route::livewire('animals/{animal}', AnimalShow::class)->name('animals.show');

    Route::livewire('upcoming', Upcoming::class)->name('upcoming.index');

    Route::livewire('health-records', HealthRecordsIndex::class)->name('health-records.index');
    Route::livewire('breeding-records', BreedingRecordsIndex::class)->name('breeding-records.index');
    Route::livewire('weight-logs', WeightLogsIndex::class)->name('weight-logs.index');
    Route::livewire('farm', FarmSettings::class)->name('farm.edit');
    Route::livewire('reports', ReportsIndex::class)->name('reports.index');
});

require __DIR__.'/settings.php';
