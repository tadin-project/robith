<?php

namespace Database\Seeders;

use App\Models\SettingSubKriteriaRadar;
use Illuminate\Database\Seeder;

class SettingSubKriteriaRadarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SettingSubKriteriaRadar::insert([[
            'md_id' => 1,
            'md_kode' => '01',
            'md_nama' => 'Direction',
            'md_status' => true,
            'md_color' => "rgba(255, 99, 132)",
        ], [
            'md_id' => 2,
            'md_kode' => '02',
            'md_nama' => 'Execution',
            'md_status' => true,
            'md_color' => "rgba(255, 159, 64)",
        ], [
            'md_id' => 3,
            'md_kode' => '03',
            'md_nama' => 'Result',
            'md_status' => true,
            'md_color' => "rgba(255, 205, 86)",
        ],]);
    }
}
