<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PermissionCategoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\Admin\UserController;

Auth::routes(['register' => false]);

Route::get('/ruxsatnomalar/webhook', [PermissionController::class, 'webhook'])->name('permission.webhook');
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/ruxsatnoma', [PermissionController::class, 'create'])->name('permission.create');
    Route::post('/ruxsatnoma', [PermissionController::class, 'store'])->name('permission.store');
    Route::get('/tekshir', [PermissionController::class, 'checkForm'])->name('permission.checkForm');
    Route::post('/tekshir', [PermissionController::class, 'check'])->name('permission.check');
    Route::get('/ruxsatnomalar', [PermissionController::class, 'index'])->name('permission.index');
    Route::get('/ruxsatnomalar/{permission}', [PermissionController::class, 'show'])->name('permission.show');

    Route::middleware('role:admin,hr')->group(function () {
        Route::post('/ruxsatnomalar/{permission}/assign', [PermissionController::class, 'assignManager'])->name('permission.assign');
        Route::get('/ruxsatnomalar/{permission}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
        Route::put('/ruxsatnomalar/{permission}', [PermissionController::class, 'update'])->name('permission.update');
        Route::delete('/ruxsatnomalar/{permission}', [PermissionController::class, 'destroy'])->name('permission.destroy');
        Route::resource('xodimlar', EmployeeController::class)->names('employees')->parameters(['xodimlar' => 'employee'])->except(['show']);
    });

    Route::middleware('role:manager,admin')->group(function () {
        Route::post('/ruxsatnomalar/{permission}/decide', [PermissionController::class, 'decide'])->name('permission.decide');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('kategoriyalar', PermissionCategoryController::class)->names('categories')->parameters(['kategoriyalar' => 'category'])->except(['show']);
        Route::resource('bolimlar', DepartmentController::class)->names('departments')->parameters(['bolimlar' => 'department'])->except(['show']);
        Route::resource('admin/foydalanuvchilar', UserController::class)->names('admin.users')->parameters(['foydalanuvchilar' => 'user'])->except(['show']);
        Route::post('/admin/foydalanuvchilar/{user}/telegram-link', [UserController::class, 'generateTelegramLink'])->name('admin.users.telegram-link');
    });
});
