<?php

use App\Http\Controllers\AppSettingsC;
use App\Http\Controllers\AsesmenC;
use App\Http\Controllers\AuthAdminC;
use App\Http\Controllers\AuthC;
use App\Http\Controllers\ConvertionValueC;
use App\Http\Controllers\DashboardC;
use App\Http\Controllers\LampiranC;
use App\Http\Controllers\LaporanPenilaianC;
use App\Http\Controllers\LaporanPenilaianUserC;
use App\Http\Controllers\MsDimensiC;
use App\Http\Controllers\MsGroupsC;
use App\Http\Controllers\MsIntroductionC;
use App\Http\Controllers\MsKategoriUsahaC;
use App\Http\Controllers\MsKriteriaC;
use App\Http\Controllers\MsLampiranC;
use App\Http\Controllers\MsMenusC;
use App\Http\Controllers\MsRadarC;
use App\Http\Controllers\MsSubKriteriaC;
use App\Http\Controllers\MsUsersC;
use App\Http\Controllers\ProfilC;
use App\Http\Controllers\SettingSubKriteriaRadarC;
use App\Http\Controllers\TenantC;
use App\Http\Controllers\ValidasiAsesmenC;
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

    Route::group(['prefix' => 'convertion-value'], function () {
        Route::get('check-duplicate', [ConvertionValueC::class, 'checkDuplicate'])->name('convertion-value.check-duplicate');
        Route::get('get-data', [ConvertionValueC::class, 'getData'])->name('convertion-value.get-data');
        Route::get('{id}', [ConvertionValueC::class, 'getById'])->name('convertion-value.get');
        Route::delete('{id}', [ConvertionValueC::class, 'delete'])->name('convertion-value.delete');
        Route::get('', [ConvertionValueC::class, 'index'])->name('convertion-value.index');
        Route::post('', [ConvertionValueC::class, 'save'])->name('convertion-value.save');
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

    Route::group(['prefix' => 'ms-kategori-usaha'], function () {
        Route::get('check-duplicate', [MsKategoriUsahaC::class, 'checkDuplicate'])->name('ms-kategori-usaha.check-duplicate');
        Route::get('get-data', [MsKategoriUsahaC::class, 'getData'])->name('ms-kategori-usaha.get-data');
        Route::get('{id}', [MsKategoriUsahaC::class, 'getById'])->name('ms-kategori-usaha.get');
        Route::delete('{id}', [MsKategoriUsahaC::class, 'delete'])->name('ms-kategori-usaha.delete');
        Route::get('', [MsKategoriUsahaC::class, 'index'])->name('ms-kategori-usaha.index');
        Route::post('', [MsKategoriUsahaC::class, 'save'])->name('ms-kategori-usaha.save');
    });

    Route::group(['prefix' => 'laporan-penilaian'], function () {
        Route::get('get-data', [LaporanPenilaianC::class, 'getData'])->name('laporan-penilaian.get-data');
        Route::get('{id}', [LaporanPenilaianC::class, 'getById'])->name('laporan-penilaian.get');
        Route::get('', [LaporanPenilaianC::class, 'index'])->name('laporan-penilaian.index');
    });

    Route::group(['prefix' => 'laporan-penilaian-user'], function () {
        Route::get('cetak', [LaporanPenilaianUserC::class, 'cetak'])->name('laporan-penilaian-user.cetak');
        Route::get('', [LaporanPenilaianUserC::class, 'index'])->name('laporan-penilaian-user.index');
    });

    Route::group(['prefix' => 'ms-lampiran'], function () {
        Route::get('check-duplicate', [MsLampiranC::class, 'checkDuplicate'])->name('ms-lampiran.check-duplicate');
        Route::get('get-data', [MsLampiranC::class, 'getData'])->name('ms-lampiran.get-data');
        Route::get('{id}', [MsLampiranC::class, 'getById'])->name('ms-lampiran.get');
        Route::delete('{id}', [MsLampiranC::class, 'delete'])->name('ms-lampiran.delete');
        Route::get('', [MsLampiranC::class, 'index'])->name('ms-lampiran.index');
        Route::post('', [MsLampiranC::class, 'save'])->name('ms-lampiran.save');
    });

    Route::group(['prefix' => 'ms-radar'], function () {
        Route::get('check-duplicate', [MsRadarC::class, 'checkDuplicate'])->name('ms-radar.check-duplicate');
        Route::get('get-data', [MsRadarC::class, 'getData'])->name('ms-radar.get-data');
        Route::get('{id}', [MsRadarC::class, 'getById'])->name('ms-radar.get');
        Route::delete('{id}', [MsRadarC::class, 'delete'])->name('ms-radar.delete');
        Route::get('', [MsRadarC::class, 'index'])->name('ms-radar.index');
        Route::post('', [MsRadarC::class, 'save'])->name('ms-radar.save');
    });

    Route::group(['prefix' => 'setting-sub-kriteria-radar'], function () {
        Route::get('get-data/{jenis}', [SettingSubKriteriaRadarC::class, 'getData'])->name('setting-sub-kriteria-radar.get-data');
        Route::get('get-parent', [SettingSubKriteriaRadarC::class, 'getParent'])->name('setting-sub-kriteria-radar.get-parent');
        Route::get('', [SettingSubKriteriaRadarC::class, 'index'])->name('setting-sub-kriteria-radar.index');
        Route::post('', [SettingSubKriteriaRadarC::class, 'save'])->name('setting-sub-kriteria-radar.save');
    });

    Route::group(['prefix' => 'ms-pengenalan'], function () {
        Route::get('check-duplicate', [MsIntroductionC::class, 'checkDuplicate'])->name('ms-pengenalan.check-duplicate');
        Route::get('get-data', [MsIntroductionC::class, 'getData'])->name('ms-pengenalan.get-data');
        Route::get('{id}', [MsIntroductionC::class, 'getById'])->name('ms-pengenalan.get');
        Route::delete('{id}', [MsIntroductionC::class, 'delete'])->name('ms-pengenalan.delete');
        Route::get('', [MsIntroductionC::class, 'index'])->name('ms-pengenalan.index');
        Route::post('', [MsIntroductionC::class, 'save'])->name('ms-pengenalan.save');
    });

    Route::group(['prefix' => 'app-settings'], function () {
        Route::get('get-data', [AppSettingsC::class, 'getData'])->name('app-settings.get-data');
        Route::get('{id}', [AppSettingsC::class, 'getById'])->name('app-settings.get');
        Route::delete('{id}', [AppSettingsC::class, 'delete'])->name('app-settings.delete');
        Route::get('', [AppSettingsC::class, 'index'])->name('app-settings.index');
        Route::post('', [AppSettingsC::class, 'save'])->name('app-settings.save');
    });

    Route::group(['prefix' => 'tenant'], function () {
        Route::get('get-data', [TenantC::class, 'getData'])->name('tenant.get-data');
        Route::get('get-users', [TenantC::class, 'getUsers'])->name('tenant.get-user');
        Route::get('{id}', [TenantC::class, 'getById'])->name('tenant.get');
        Route::delete('{id}', [TenantC::class, 'delete'])->name('tenant.delete');
        Route::get('', [TenantC::class, 'index'])->name('tenant.index');
        Route::post('', [TenantC::class, 'save'])->name('tenant.save');
    });

    Route::group(['prefix' => 'asesmen'], function () {
        Route::get('cek-data', [AsesmenC::class, 'cekData'])->name('asesmen.cek-data');
        Route::post('save-tmp', [AsesmenC::class, 'saveSementara'])->name('asesmen.save-tmp');
        Route::post('save', [AsesmenC::class, 'save'])->name('asesmen.save');
        Route::get('', [AsesmenC::class, 'index'])->name('asesmen.index');
    });

    Route::group(['prefix' => 'validasi-asesmen'], function () {
        Route::get('edit/{id}', [ValidasiAsesmenC::class, 'edit'])->name('validasi-asesmen.edit');
        Route::post('final/{id}', [ValidasiAsesmenC::class, 'final'])->name('validasi-asesmen.final');
        Route::get('get-data', [ValidasiAsesmenC::class, 'getData'])->name('validasi-asesmen.get-data');
        Route::post('validasi', [ValidasiAsesmenC::class, 'validasi'])->name('validasi-asesmen.validasi');
        Route::get('', [ValidasiAsesmenC::class, 'index'])->name('validasi-asesmen.index');
    });

    Route::group(['prefix' => 'profil'], function () {
        Route::post('ganti-password', [ProfilC::class, 'savePass'])->name('profil.save-password');
        Route::get('', [ProfilC::class, 'index'])->name('profil.index');
        Route::post('', [ProfilC::class, 'saveProfil'])->name('profil.save-profil');
    });

    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('get-data-tenant', [DashboardC::class, 'getDataTenant'])->name('dashboard.get-data-tenant');
        Route::get('', [DashboardC::class, 'index'])->name('dashboard.index');
    });

    Route::group(['prefix' => 'lampiran'], function () {
        Route::get('', [LampiranC::class, 'index'])->name('lampiran.index');
    });

    Route::get('logout', [AuthC::class, 'logout'])->name('logout');

    Route::get('/admin', [AuthAdminC::class, 'index'])->name('auth-admin.index');
    Route::post('/admin', [AuthAdminC::class, 'login'])->name('auth-admin.login');
    Route::post('login', [AuthC::class, 'prosesLogin'])->name('auth.login.post');
    Route::get('register', [AuthC::class, 'register'])->name('auth.register');
    Route::post('register', [AuthC::class, 'prosesRegister'])->name('auth.register.post');
    Route::get('forgot', [AuthC::class, 'forgot'])->name('auth.forgot');
    Route::post('forgot', [AuthC::class, 'prosesForgot'])->name('auth.forgot.post');
    Route::get('reset', [AuthC::class, 'reset'])->name('auth.reset');
    Route::post('reset', [AuthC::class, 'prosesReset'])->name('auth.reset.post');
    Route::get('/', [AuthC::class, 'index'])->name('auth.index');
});

Route::get('activate', [AuthC::class, 'aktifasiAkun'])->name('auth.activate');
