<?php

namespace Database\Seeders;

use App\Models\MsKategoriUsaha;
use Illuminate\Database\Seeder;

class MsKategoriUsahaSeeder extends Seeder
{
    public function run()
    {
        MsKategoriUsaha::insert([[
            'mku_id' => 1,
            'mku_kode' => '01',
            'mku_nama' => 'Kuliner/F&B',
            'mku_status' => true,
        ], [
            'mku_id' => 2,
            'mku_kode' => '02',
            'mku_nama' => 'Teknologi',
            'mku_status' => true,
        ], [
            'mku_id' => 3,
            'mku_kode' => '03',
            'mku_nama' => 'Industri Kreatif',
            'mku_status' => true,
        ], [
            'mku_id' => 4,
            'mku_kode' => '04',
            'mku_nama' => 'Pendidikan',
            'mku_status' => true,
        ], [
            'mku_id' => 5,
            'mku_kode' => '05',
            'mku_nama' => 'Pertanian',
            'mku_status' => true,
        ], [
            'mku_id' => 6,
            'mku_kode' => '06',
            'mku_nama' => 'Maritim',
            'mku_status' => true,
        ],]);
    }
}
