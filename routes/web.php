<?php

use App\Http\Controllers\AuthAdminC;
use App\Http\Controllers\AuthC;
use App\Http\Controllers\DashboardC;
use App\Http\Controllers\MsDimensiC;
use App\Http\Controllers\MsGroupsC;
use App\Http\Controllers\MsKategoriC;
use App\Http\Controllers\MsKriteriaC;
use App\Http\Controllers\MsMenusC;
use App\Http\Controllers\MsSubKriteriaC;
use App\Http\Controllers\MsUsersC;
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

Route::group(["middleware" => "has_auth"], function () {
    Route::group(['prefix' => 'ms-groups'], function () {
        Route::get('akses', [MsGroupsC::class, 'getAkses'])->name('ms-groups.get-akses');
        Route::post('akses', [MsGroupsC::class, 'saveAkses'])->name('ms-groups.save-akses');
        Route::get('check-duplicate', [MsGroupsC::class, 'checkDuplicate'])->name('ms-groups.check-duplicate');
        Route::get('get-data', [MsGroupsC::class, 'getData'])->name('ms-groups.get-data');
        Route::get('{id}', [MsGroupsC::class, 'getById'])->name('ms-groups.get');
        Route::delete('{id}', [MsGroupsC::class, 'delete'])->name('ms-groups.delete');
        Route::get('', [MsGroupsC::class, 'index'])->name('ms-groups.index');
        Route::post('', [MsGroupsC::class, 'save'])->name('ms-groups.save');
    });

    Route::group(['prefix' => 'ms-users'], function () {
        Route::get('check-duplicate', [MsUsersC::class, 'checkDuplicate'])->name('ms-users.check-duplicate');
        Route::get('get-data', [MsUsersC::class, 'getData'])->name('ms-users.get-data');
        Route::get('{id}', [MsUsersC::class, 'getById'])->name('ms-users.get');
        Route::delete('{id}', [MsUsersC::class, 'delete'])->name('ms-users.delete');
        Route::get('', [MsUsersC::class, 'index'])->name('ms-users.index');
        Route::post('', [MsUsersC::class, 'save'])->name('ms-users.save');
    });

    Route::group(['prefix' => 'ms-menus'], function () {
        Route::get('check-duplicate', [MsMenusC::class, 'checkDuplicate'])->name('ms-menus.check-duplicate');
        Route::get('get-parent', [MsMenusC::class, 'getParent'])->name('ms-menus.get-parent');
        Route::get('get-data', [MsMenusC::class, 'getData'])->name('ms-menus.get-data');
        Route::get('{id}', [MsMenusC::class, 'getById'])->name('ms-menus.get');
        Route::delete('{id}', [MsMenusC::class, 'delete'])->name('ms-menus.delete');
        Route::get('', [MsMenusC::class, 'index'])->name('ms-menus.index');
        Route::post('', [MsMenusC::class, 'save'])->name('ms-menus.save');
    });

    Route::group(['prefix' => 'ms-dimensi'], function () {
        Route::get('check-duplicate', [MsDimensiC::class, 'checkDuplicate'])->name('ms-dimensi.check-duplicate');
        Route::get('get-data', [MsDimensiC::class, 'getData'])->name('ms-dimensi.get-data');
        Route::get('{id}', [MsDimensiC::class, 'getById'])->name('ms-dimensi.get');
        Route::delete('{id}', [MsDimensiC::class, 'delete'])->name('ms-dimensi.delete');
        Route::get('', [MsDimensiC::class, 'index'])->name('ms-dimensi.index');
        Route::post('', [MsDimensiC::class, 'save'])->name('ms-dimensi.save');
    });

    Route::group(['prefix' => 'ms-kriteria'], function () {
        Route::get('check-duplicate', [MsKriteriaC::class, 'checkDuplicate'])->name('ms-kriteria.check-duplicate');
        Route::get('get-data', [MsKriteriaC::class, 'getData'])->name('ms-kriteria.get-data');
        Route::get('{id}', [MsKriteriaC::class, 'getById'])->name('ms-kriteria.get');
        Route::delete('{id}', [MsKriteriaC::class, 'delete'])->name('ms-kriteria.delete');
        Route::get('', [MsKriteriaC::class, 'index'])->name('ms-kriteria.index');
        Route::post('', [MsKriteriaC::class, 'save'])->name('ms-kriteria.save');
    });

    Route::group(['prefix' => 'ms-sub-kriteria'], function () {
        Route::get('check-duplicate', [MsSubKriteriaC::class, 'checkDuplicate'])->name('ms-sub-kriteria.check-duplicate');
        Route::get('get-data', [MsSubKriteriaC::class, 'getData'])->name('ms-sub-kriteria.get-data');
        Route::get('get-kriteria', [MsSubKriteriaC::class, 'getKriteria'])->name('ms-sub-kriteria.get-kriteria');
        Route::get('{id}', [MsSubKriteriaC::class, 'getById'])->name('ms-sub-kriteria.get');
        Route::delete('{id}', [MsSubKriteriaC::class, 'delete'])->name('ms-sub-kriteria.delete');
        Route::get('', [MsSubKriteriaC::class, 'index'])->name('ms-sub-kriteria.index');
        Route::post('', [MsSubKriteriaC::class, 'save'])->name('ms-sub-kriteria.save');
    });

    Route::group(['prefix' => 'profil'], function () {
        Route::get('', [ProfilC::class, 'index'])->name('profil.index');
    });

    Route::get('dashboard', [DashboardC::class, 'index'])->name('dashboard');
    Route::get('logout', [AuthC::class, 'logout'])->name('logout');

    Route::get('/', [AuthC::class, 'index'])->name('auth.index');
    Route::post('/', [AuthC::class, 'login'])->name('auth.post');
    Route::get('/auth/admin', [AuthAdminC::class, 'index'])->name('auth-admin.index');
    Route::post('/auth/admin', [AuthAdminC::class, 'login'])->name('auth-admin.login');
});
