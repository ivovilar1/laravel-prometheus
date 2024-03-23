<?php

use App\Enum\Can;
use App\Livewire\Auth\{EmailValidation, Login, Logout, Password, Register};
use App\Livewire\{Admin, Customers, Welcome, Opportunities};
use Illuminate\Support\Facades\Route;

//region Login Flow
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('auth.register');
Route::get('/logout', Logout::class)->name('auth.logout');
Route::get('/email-validation', EmailValidation::class)->middleware('auth')->name('auth.email-validation');
Route::get('/password/recovery', Password\Recovery::class)->name('password.recovery');
Route::get('/password/reset', Password\Reset::class)->name('password.reset');
//endregion

//region Authenticated
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', Welcome::class)->name('dashboard');

    //region Customers
    Route::get('/customers', Customers\Index::class)->name('customers');
    //endregion

    //region Opportunities
    Route::get('/opportunities', Opportunities\Index::class)->name('opportunities');
    //endregion

    //region Admin
    Route::prefix('/admin')->middleware('can:' . Can::BE_AN_ADMIN->value)->group(function () {
        Route::get('/dashboard', Admin\Dashboard::class)->name('admin.dashboard');
        Route::get('/users', Admin\Users\Index::class)->name('admin.users');
    });
    //endregion

});
//endregion
