<?php

namespace Database\Seeders;

use App\Models\ConvertionValue;
use Illuminate\Database\Seeder;

class ConvertionValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConvertionValue::insert([[
            'cval_id' => 1,
            'cval_kode' => '01',
            'cval_nama' => 'Sangat Buruk',
            'cval_status' => true,
            'cval_nilai' => 20,
        ], [
            'cval_id' => 2,
            'cval_kode' => '02',
            'cval_nama' => 'Buruk',
            'cval_status' => true,
            'cval_nilai' => 40,
        ], [
            'cval_id' => 3,
            'cval_kode' => '03',
            'cval_nama' => 'Sedang',
            'cval_status' => true,
            'cval_nilai' => 60,
        ], [
            'cval_id' => 4,
            'cval_kode' => '04',
            'cval_nama' => 'Baik',
            'cval_status' => true,
            'cval_nilai' => 80,
        ], [
            'cval_id' => 5,
            'cval_kode' => '05',
            'cval_nama' => 'Sangat Baik',
            'cval_status' => true,
            'cval_nilai' => 100,
        ],]);
    }
}
