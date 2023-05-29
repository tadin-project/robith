<?php

namespace Database\Seeders;

use App\Models\MsRadar;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Seeder;

class MsRadarSeeder extends Seeder
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

        $table = new MsRadar();
        $this->truncate($table->getTable());
        MsRadar::insert([[
            'mr_id' => 1,
            'mr_kode' => '0101',
            'mr_nama' => 'Approches',
            'mr_status' => true,
            'mr_bobot' => 20,
        ], [
            'mr_id' => 2,
            'mr_kode' => '0102',
            'mr_nama' => 'Deployment',
            'mr_status' => true,
            'mr_bobot' => 30,
        ], [
            'mr_id' => 3,
            'mr_kode' => '0103',
            'mr_nama' => 'Assessment & Refinement',
            'mr_status' => true,
            'mr_bobot' => 50,
        ], [
            'mr_id' => 4,
            'mr_kode' => '0201',
            'mr_nama' => 'Relevance & Usability',
            'mr_status' => true,
            'mr_bobot' => 50,
        ], [
            'mr_id' => 5,
            'mr_kode' => '0202',
            'mr_nama' => 'Performance',
            'mr_status' => true,
            'mr_bobot' => 50,
        ],]);

        $this->enableForeignKeys();
    }
}
