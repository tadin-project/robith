<?php

namespace Database\Seeders;

use App\Models\MsKriteria;
use Illuminate\Database\Seeder;

class MsKriteriaSeeder extends Seeder
{
    public function run()
    {
        MsKriteria::insert([[
            'mk_id' => 1,
            'mk_kode' => '01',
            'mk_nama' => 'Purpose, Vision & Strategy',
            'mk_status' => true,
            'md_id' => 1,
        ], [
            'mk_id' => 2,
            'mk_kode' => '02',
            'mk_nama' => 'Organisational Culture & Leadership',
            'mk_status' => true,
            'md_id' => 1,
        ], [
            'mk_id' => 3,
            'mk_kode' => '01',
            'mk_nama' => 'Engaging Stakeholders',
            'mk_status' => true,
            'md_id' => 2,
        ], [
            'mk_id' => 4,
            'mk_kode' => '02',
            'mk_nama' => 'Creating Sustainable Value',
            'mk_status' => true,
            'md_id' => 2,
        ], [
            'mk_id' => 5,
            'mk_kode' => '03',
            'mk_nama' => 'Driving Performance & Transformation',
            'mk_status' => true,
            'md_id' => 2,
        ], [
            'mk_id' => 6,
            'mk_kode' => '01',
            'mk_nama' => 'Strategic & Operational Performance',
            'mk_status' => true,
            'md_id' => 3,
        ], [
            'mk_id' => 7,
            'mk_kode' => '02',
            'mk_nama' => 'Stakeholder Perceptions',
            'mk_status' => true,
            'md_id' => 3,
        ],]);
    }
}
