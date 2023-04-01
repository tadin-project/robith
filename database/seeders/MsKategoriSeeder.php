<?php

namespace Database\Seeders;

use App\Models\MsKategori;
use Illuminate\Database\Seeder;

class MsKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MsKategori::insert([[
            'mk_id' => 1,
            'mk_kode' => '01',
            'mk_nama' => 'Direction',
            'mk_status' => true,
        ], [
            'mk_id' => 2,
            'mk_kode' => '02',
            'mk_nama' => 'Execution',
            'mk_status' => true,
        ], [
            'mk_id' => 3,
            'mk_kode' => '03',
            'mk_nama' => 'Result',
            'mk_status' => true,
        ],]);
    }
}
