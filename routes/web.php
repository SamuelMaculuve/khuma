<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Users;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\LeadsController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/leads', function () {
    return view('admin.whatsapp.index');
})->name("leads.all");

//Route::get('/whatsapp-show', function () {
//    return view('admin.whatsapp.show');
//});

Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/users', Users::class)->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::post('/instance/connect', [InstanceController::class, 'connect'])->name('instance.connect');
    Route::resource('instance', InstanceController::class);
    Route::resource('lead', LeadsController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('call_logs', App\Http\Controllers\CallLogController::class)->middleware(['auth']);


require __DIR__.'/auth.php';
