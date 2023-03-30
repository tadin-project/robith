<?php

use App\Http\Controllers\AuthC;
use App\Http\Controllers\MsGroupsC;
use App\Http\Controllers\ProfilC;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'ms-groups'], function () {
    Route::get('', [MsGroupsC::class, 'index'])->name('ms-groups.index');
    Route::post('', [MsGroupsC::class, 'save'])->name('ms-groups.save');
    Route::get('akses', [MsGroupsC::class, 'getAkses'])->name('ms-groups.get-akses');
    Route::post('akses', [MsGroupsC::class, 'saveAkses'])->name('ms-groups.save-akses');
    Route::get('check-duplicate', [MsGroupsC::class, 'checkDuplicate'])->name('ms-groups.check-duplicate');
    Route::get('get-data', [MsGroupsC::class, 'getData'])->name('ms-groups.get-data');
    Route::get('{id}', [MsGroupsC::class, 'getById'])->name('ms-groups.get');
    Route::delete('{id}', [MsGroupsC::class, 'delete'])->name('ms-groups.delete');
});

Route::group(['prefix' => 'profil'], function () {
    Route::get('', [ProfilC::class, 'index'])->name('profil.index');
});

Route::get('logout', [AuthC::class, 'logout'])->name('logout');
Route::get('/', function () {
    return view('welcome');
});
