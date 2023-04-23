<?php

namespace Database\Seeders;

use App\Models\MsGroups;
use Illuminate\Database\Seeder;

class MsGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MsGroups::insert([
            [
                'group_id' => 1,
                'group_kode' => '00',
                'group_nama' => 'Admin Vendor',
                'group_status' => true,
            ], [
                'group_id' => 2,
                'group_kode' => '01',
                'group_nama' => 'Administrator',
                'group_status' => true,
            ], [
                'group_id' => 3,
                'group_kode' => '02',
                'group_nama' => 'User',
                'group_status' => true,
            ], [
                'group_id' => 4,
                'group_kode' => '03',
                'group_nama' => 'Validator',
                'group_status' => true,
            ],
        ]);
    }
}
