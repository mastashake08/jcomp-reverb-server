<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return redirect()->route('applications.index');
    })->name('dashboard');
    
    // Application Management Routes
    Route::resource('applications', ApplicationController::class);
    Route::post('applications/{application}/health', [ApplicationController::class, 'health'])
        ->name('applications.health');
    Route::post('applications/{application}/toggle-status', [ApplicationController::class, 'updateStatus'])
        ->name('applications.toggle-status');
});

require __DIR__.'/settings.php';
