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
use App\Livewire\Subscription\Confirm;
use App\Livewire\Subscription\Dashboard as SubscriptionDashboard;
use App\Livewire\CompanyManagement;
use App\Livewire\Admin\Plans as AdminPlans;
use App\Http\Controllers\SubscriptionReceiptController;
use App\Http\Controllers\EmailCampaignTrackingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email-campaigns/track/open/{token}', [EmailCampaignTrackingController::class, 'open'])
    ->name('email-campaigns.track.open');
Route::get('/email-campaigns/track/click/{token}', [EmailCampaignTrackingController::class, 'click'])
    ->name('email-campaigns.track.click');

Route::get('dashboard', [DashboardController::class, 'dashboard'])->middleware('auth')->name('dashboard');
Route::get('dashboard/report', [DashboardController::class, 'downloadReport'])->middleware(['auth', 'feature:reports'])->name('dashboard.report');

Route::middleware('auth')->group(function () {
    Route::middleware('admin')->group(function () {
        Route::get('/users', Users::class)->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
        Route::get('/admin/plans', AdminPlans::class)->name('admin.plans');
    });

    Route::post('/instance/connect', [InstanceController::class, 'connect'])->middleware('feature:whatsapp')->name('instance.connect');
    Route::resource('instance', InstanceController::class)->middleware('feature:whatsapp');
    Route::resource('lead', LeadsController::class)->middleware('feature:crm');

    // CRM — direct Livewire full-page
    Route::get('/leads', LeadsManager::class)->middleware('feature:crm')->name('leads.all');

    Route::get('/email-campaigns', EmailCampaigns::class)->middleware('feature:email_campaigns')->name('email-campaigns.index');
    Route::get('/settings', Settings::class)->middleware('feature:settings')->name('settings.index');

    Route::get('/manage', CompanyManagement::class)->middleware('feature:team_management')->name('manage');

    // Subscription — direct Livewire full-page
    Route::get('/subscription', SubscriptionDashboard::class)->name('subscription.dashboard');
    Route::get('/subscription/receipts/{payment}', [SubscriptionReceiptController::class, 'show'])->name('subscription.receipts.show');
    Route::get('/subscription/plans', ChoosePlan::class)->name('subscription.plans');
    Route::get('/subscription/payment/{plan}', Confirm::class)->name('subscription.checkout');
    Route::view('/subscription/success', 'subscription.success')->name('subscription.success');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('call_logs', App\Http\Controllers\CallLogController::class)->middleware(['auth', 'feature:call_logs']);

require __DIR__ . '/auth.php';
