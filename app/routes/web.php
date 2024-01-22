<?php

use App\Http\Middleware\AlwaysRedirectToLogin;
use App\Models\User;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use ProtoneMedia\SpladeCore\Http\Refreshable;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::view('/anonymous', 'anonymous');
Route::view('/base-view', 'base-view');
Route::view('/blade-method', 'blade-method');
Route::view('/blade-method-callbacks', 'blade-method-callbacks');
Route::view('/change-blade-prop', 'change-blade-prop');
Route::view('/component-import', 'component-import');
Route::view('/dynamic', 'dynamic')->withoutMiddleware(SubstituteBindings::class);
Route::view('/dynamic-component-import', 'dynamic-component-import');
Route::view('/emit', 'emit');
Route::view('/form', 'form');
Route::view('/props-in-template', 'props-in-template');
Route::view('/refresh', 'refresh')->middleware(Refreshable::class);
Route::view('/refresh-state', 'refresh-state')->middleware(Refreshable::class);
Route::view('/regular-view', 'regular-view');
Route::view('/slot', 'slot');
Route::view('/to-vue-prop', 'to-vue-prop');
Route::view('/two-way-binding', 'two-way-binding');

Route::get('/login', function () {
    return 'login!';
})->name('login');

Route::view('/redirect/change-blade-prop', 'change-blade-prop')
    ->middleware(AlwaysRedirectToLogin::class)
    ->name('redirect.change-blade-prop');

Route::middleware('auth')->prefix('/auth/')->group(function () {
    Route::view('/change-blade-prop', 'change-blade-prop')->name('auth.change-blade-prop');

    Route::get('/change-blade-prop/{user}', function (User $user) {

    })->name('auth.change-blade-prop.user');
});
