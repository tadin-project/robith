<?php

namespace Database\Seeders;

use App\Models\MsMenus;
use Illuminate\Database\Seeder;

class MsMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MsMenus::insert([[
            'menu_id' => 1,
            'menu_kode' => '01',
            'menu_nama' => 'Administrator',
            'menu_status' => true,
            'menu_type' => 2,
            'menu_link' => null,
            'menu_ikon' => null,
            'parent_menu_id' => 0,
        ], [
            'menu_id' => 2,
            'menu_kode' => '01.01',
            'menu_nama' => 'Setting',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "#",
            'menu_ikon' => "",
            'parent_menu_id' => 1,
        ], [
            'menu_id' => 3,
            'menu_kode' => '01.01.01',
            'menu_nama' => 'Master Hak Akses',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "ms-groups",
            'menu_ikon' => "",
            'parent_menu_id' => 2,
        ], [
            'menu_id' => 4,
            'menu_kode' => '01.01.02',
            'menu_nama' => 'Master User',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "ms-users",
            'menu_ikon' => "",
            'parent_menu_id' => 2,
        ], [
            'menu_id' => 5,
            'menu_kode' => '01.01.03',
            'menu_nama' => 'Master Menu',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "ms-menus",
            'menu_ikon' => "",
            'parent_menu_id' => 2,
        ], [
            'menu_id' => 6,
            'menu_kode' => '01.01.04',
            'menu_nama' => 'Setting Web',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "app-settings",
            'menu_ikon' => "",
            'parent_menu_id' => 2,
        ], [
            'menu_id' => 7,
            'menu_kode' => '00',
            'menu_nama' => 'Dashboard',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "dashboard",
            'menu_ikon' => "",
            'parent_menu_id' => 0,
        ],]);
    }
}
