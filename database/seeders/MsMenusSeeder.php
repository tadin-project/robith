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
        ], [
            'menu_id' => 8,
            'menu_kode' => '01.02',
            'menu_nama' => 'Master Data',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "#",
            'menu_ikon' => "",
            'parent_menu_id' => 1,
        ], [
            'menu_id' => 9,
            'menu_kode' => '01.02.01',
            'menu_nama' => 'Master Dimensi',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "ms-dimensi",
            'menu_ikon' => "",
            'parent_menu_id' => 8,
        ], [
            'menu_id' => 10,
            'menu_kode' => '01.02.02',
            'menu_nama' => 'Master Kriteria',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "ms-kriteria",
            'menu_ikon' => "",
            'parent_menu_id' => 8,
        ], [
            'menu_id' => 11,
            'menu_kode' => '01.02.03',
            'menu_nama' => 'Master Sub Kriteria',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "ms-sub-kriteria",
            'menu_ikon' => "",
            'parent_menu_id' => 8,
        ], [
            'menu_id' => 12,
            'menu_kode' => '01.02.04',
            'menu_nama' => 'Master Kategori Usaha',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "ms-kategori-usaha",
            'menu_ikon' => "",
            'parent_menu_id' => 8,
        ], [
            'menu_id' => 13,
            'menu_kode' => '01.02.05',
            'menu_nama' => 'Master Lampiran',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "ms-lampiran",
            'menu_ikon' => "",
            'parent_menu_id' => 8,
        ], [
            'menu_id' => 14,
            'menu_kode' => '01.03',
            'menu_nama' => 'Data Tenant',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "tenant",
            'menu_ikon' => "",
            'parent_menu_id' => 1,
        ], [
            'menu_id' => 15,
            'menu_kode' => '02',
            'menu_nama' => 'User',
            'menu_status' => true,
            'menu_type' => 2,
            'menu_link' => "",
            'menu_ikon' => "",
            'parent_menu_id' => 0,
        ], [
            'menu_id' => 16,
            'menu_kode' => '02.01',
            'menu_nama' => 'Asesmen',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "asesmen",
            'menu_ikon' => "",
            'parent_menu_id' => 15,
        ], [
            'menu_id' => 17,
            'menu_kode' => '03',
            'menu_nama' => 'Validator',
            'menu_status' => true,
            'menu_type' => 2,
            'menu_link' => "",
            'menu_ikon' => "",
            'parent_menu_id' => 0,
        ], [
            'menu_id' => 18,
            'menu_kode' => '03.01',
            'menu_nama' => 'Validasi Asesmen',
            'menu_status' => true,
            'menu_type' => 1,
            'menu_link' => "validasi-asesmen",
            'menu_ikon' => "",
            'parent_menu_id' => 17,
        ],]);
    }
}
