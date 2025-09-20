<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;

Auth::routes();

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
});
    Route::get('/ruxsatnomalar/webhook', [PermissionController::class, 'webhook'])->name('permission.webhook');



