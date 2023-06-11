<?php

namespace Database\Seeders;

use App\Models\MsIntroduction;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;

class MsIntroductionSeeder extends Seeder
{
    use TruncateTable, DisableForeignKeys;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        $table = new MsIntroduction();
        $this->truncate($table->getTable());
        MsIntroduction::insert([
            [
                'mi_kode' => '01',
                'mi_nama' => 'Intro 1',
                'mi_isi' => 'Ini%20adalah%20intro%201',
                'mi_status' => true,
            ], [
                'mi_kode' => '02',
                'mi_nama' => 'Intro 2',
                'mi_isi' => 'Ini%20adalah%20intro%202',
                'mi_status' => true,
            ], [
                'mi_kode' => '03',
                'mi_nama' => 'Intro 3',
                'mi_isi' => 'Ini%20adalah%20intro%203',
                'mi_status' => true,
            ],
        ]);

        $this->enableForeignKeys();
    }
}
