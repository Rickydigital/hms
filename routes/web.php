<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\Admin\{
    DashboardController,
    RolePermissionController, LabTestController, MedicineController,
    MedicineLogController,
    StoreItemController, WardController, SupplierController, SettingController
};
use App\Http\Controllers\BillingController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\MedicinePurchaseController;
use App\Http\Controllers\PharmacyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// routes/web.php
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Patients
    Route::resource('patients', PatientController::class)->only(['index', 'store']);
    Route::post('/patients/{patient}/visit', [VisitController::class, 'store'])->name('patients.visit.store');
    Route::post('patients/{patient}/reactivate', [PatientController::class, 'reactivate'])->name('patients.reactivate');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //laboratories
    Route::get('/lab', [LabController::class, 'index'])->name('lab.index');
    Route::get('/lab/order/{order}', [LabController::class, 'show'])->name('lab.order.show');
    Route::post('/lab/order/{order}/result', [LabController::class, 'storeResult'])->name('lab.result.store');

    //pharmacy
    Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('pharmacy.index');
    Route::post('/pharmacy/issue/{order}', [PharmacyController::class, 'issue'])->name('pharmacy.issue');

    //purchase
    Route::middleware(['auth'])->prefix('store')->group(function () {
    Route::get('/purchase', [MedicinePurchaseController::class, 'index'])->name('store.purchase.index');
    Route::post('/purchase', [MedicinePurchaseController::class, 'store'])->name('store.purchase.store');
    Route::get('/purchase/create', [MedicinePurchaseController::class, 'create'])->name('store.purchase.create');


});

//billing
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/search', [BillingController::class, 'search'])->name('billing.search');
    Route::get('/billing/pending/{visit}', [BillingController::class, 'showBill'])->name('billing.pending.show');
    Route::post('/billing/generate/{visit}', [BillingController::class, 'generateReceipt'])->name('billing.generate');
    Route::post('/billing/pay/{visit}', [BillingController::class, 'recordPayment'])
         ->name('billing.pay');

    Route::get('/billing/payment-details/{visit}', [BillingController::class, 'paymentDetails'])
    ->name('billing.payment-details');

    // ========================= DOCTOR ROUTES (Simple & Clean) =========================
    Route::prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/opd', [DoctorController::class, 'index'])->name('opd');
        Route::get('/opd/{visit}', [DoctorController::class, 'show'])->name('opd.show');
        Route::post('/vitals/{visit}', [DoctorController::class, 'storeVitals'])->name('vitals.store');
        Route::post('/prescription/{visit}', [DoctorController::class, 'storePrescription'])->name('prescription.store');
    });
    // ===============================================================================

    // Admin Only Routes
    Route::middleware('role:Admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);
        Route::get('roles', [RolePermissionController::class, 'index'])->name('admin.roles');
        Route::post('roles', [RolePermissionController::class, 'store']);
        Route::put('roles/{role}', [RolePermissionController::class, 'update']);

        Route::resource('lab-tests', LabTestController::class)->except(['show']);
        Route::resource('medicines', MedicineController::class)->except(['show']);
        Route::resource('store-items', StoreItemController::class)->except(['show']);
        Route::resource('wards', WardController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);

        Route::prefix('admin')->middleware(['auth', 'role:Admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/medicine-logs', [MedicineLogController::class, 'index'])->name('admin.medicine.logs');
        Route::get('/medicine-logs/filter', [MedicineLogController::class, 'filter'])->name('admin.medicine.logs.filter');
        });
    });

    // Settings (Admin only)
    Route::prefix('admin')->middleware(['auth', 'permission:manage settings'])->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('admin.settings');
        Route::post('settings', [SettingController::class, 'update']);
        Route::post('settings/custom', [SettingController::class, 'storeCustom']);
        Route::delete('settings/delete/{key}', [SettingController::class, 'delete']);
    });
});

require __DIR__.'/auth.php';