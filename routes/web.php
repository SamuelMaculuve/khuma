<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Users;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\LeadsController;
use App\Livewire\Subscription\ChoosePlan;
use App\Livewire\Subscription\Checkout;
use App\Livewire\Subscription\Confirm;
use App\Livewire\Subscription\Success;


Route::get('/', function () {
    return view('welcome');
});

Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/users', Users::class)->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::post('/instance/connect', [InstanceController::class, 'connect'])->name('instance.connect');
    Route::resource('instance', InstanceController::class);
    Route::resource('lead', LeadsController::class);

    Route::get('/leads', function () {
        return view('admin.whatsapp.index');
    })->name("leads.all");

    Route::get('/manage', function () {
        return view('admin.manage.index');
    })->name('manage');

    Route::get('/subscription/plans', function () {
        return view('subscription.index');
    })->name('subscription.plans');

    Route::get('/subscription/checkout/{plan}', Checkout::class)->name('subscription.checkout');
    Route::get('/subscription/confirm/{plan}', Confirm::class)->name('subscription.confirm');
    Route::view('/subscription/success', 'subscription.success')->name('subscription.success');

    // Perfil com Livewire (substitui os controllers do Breeze para profile)
    Route::get('/profile', \App\Livewire\Profile::class)->name('profile.edit');
    // Histórico de pagamentos
    Route::get('/subscription/payments', \App\Livewire\Subscription\Payments::class)
        ->name('subscription.payments');

    // Download de comprovativo PDF
    Route::get('/receipt/{payment}/download', [\App\Http\Controllers\ReceiptController::class, 'download'])
        ->name('receipt.download');

    Route::get('/planos', \App\Livewire\Plans\PlanList::class)->name('plans.index');
});

Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('call_logs', App\Http\Controllers\CallLogController::class)->middleware(['auth']);


require __DIR__ . '/auth.php';
