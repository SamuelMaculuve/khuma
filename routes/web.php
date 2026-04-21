<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Users;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\LeadsController;
use App\Livewire\EmailCampaigns;
use App\Livewire\LeadsManager;
use App\Livewire\Settings;
use App\Livewire\Subscription\ChoosePlan;
use App\Livewire\Subscription\Checkout;
use App\Livewire\Subscription\Confirm;
use App\Livewire\CompanyManagement;

Route::get('/', function () {
    return view('welcome');
});

Route::get('dashboard', [DashboardController::class, 'dashboard'])->middleware('auth')->name('dashboard');
Route::get('dashboard/report', [DashboardController::class, 'downloadReport'])->middleware('auth')->name('dashboard.report');

Route::middleware('auth')->group(function () {
    Route::get('/users', Users::class)->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');

    Route::post('/instance/connect', [InstanceController::class, 'connect'])->name('instance.connect');
    Route::resource('instance', InstanceController::class);
    Route::resource('lead', LeadsController::class);

    // CRM — direct Livewire full-page
    Route::get('/leads', LeadsManager::class)->name('leads.all');

    Route::get('/email-campaigns', EmailCampaigns::class)->name('email-campaigns.index');
    Route::get('/settings', Settings::class)->name('settings.index');

    Route::get('/manage', CompanyManagement::class)->name('manage');

    // Subscription — direct Livewire full-page
    Route::get('/subscription/plans', ChoosePlan::class)->name('subscription.plans');
    Route::get('/subscription/payment/{plan}', Confirm::class)->name('subscription.checkout');
    Route::view('/subscription/success', 'subscription.success')->name('subscription.success');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('call_logs', App\Http\Controllers\CallLogController::class)->middleware(['auth']);

require __DIR__ . '/auth.php';
