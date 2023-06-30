<?php

namespace Database\Seeders;

use App\Models\AppSettings;
use Illuminate\Database\Seeder;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AppSettings::insert([[
            'as_id' => 1,
            'as_key' => 'app_nama',
            'as_value' => 'AdminLte',
            'as_nama' => 'Judul Aplikasi',
            'as_desc' => 'Judul Aplikasi',
            'as_default' => 'AdminLte',
            'is_auto' => 'Y',
            'as_jenis' => 1,
        ], [
            'as_id' => 2,
            'as_key' => 'dev_nama',
            'as_value' => 'Tadin',
            'as_nama' => 'Nama Developer',
            'as_desc' => 'Nama Developer',
            'as_default' => 'Tadin',
            'is_auto' => 'Y',
            'as_jenis' => 1,
        ], [
            'as_id' => 3,
            'as_key' => 'id_tenant',
            'as_value' => '3',
            'as_nama' => 'Tenant Id',
            'as_desc' => 'Id Hak Akses untuk tenant',
            'as_default' => '3',
            'is_auto' => 'Y',
            'as_jenis' => 1,
        ], [
            'as_id' => 4,
            'as_key' => 'duration_token_reset_password',
            'as_value' => 10,
            'as_nama' => 'Durasi token reset password',
            'as_desc' => 'Durasi token reset password dalam 10 menit',
            'as_default' => 10,
            'is_auto' => 'Y',
            'as_jenis' => 1,
        ], [
            'as_id' => 5,
            'as_key' => 'background_auth',
            'as_value' => 'background.png',
            'as_nama' => 'Background Autentikasi',
            'as_desc' => 'Background autentikasi',
            'as_default' => '',
            'is_auto' => 'Y',
            'as_jenis' => 2,
        ], [
            'as_id' => 6,
            'as_key' => 'app_logo',
            'as_value' => 'logo.png',
            'as_nama' => 'Logo Aplikasi',
            'as_desc' => 'Logo Aplikasi',
            'as_default' => 'AdminLTELogo.png',
            'is_auto' => 'Y',
            'as_jenis' => 2,
        ],]);
    }
}
