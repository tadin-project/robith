<?php

namespace Database\Seeders;

use App\Models\MsDimensi;
use Illuminate\Database\Seeder;

class MsDimensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MsDimensi::insert([[
            'md_id' => 1,
            'md_kode' => '01',
            'md_nama' => 'Direction',
            'md_status' => true,
            'md_color' => "#FF6384",
        ], [
            'md_id' => 2,
            'md_kode' => '02',
            'md_nama' => 'Execution',
            'md_status' => true,
            'md_color' => "#FF9F40",
        ], [
            'md_id' => 3,
            'md_kode' => '03',
            'md_nama' => 'Result',
            'md_status' => true,
            'md_color' => "#FFCD56",
        ],]);
    }
}
