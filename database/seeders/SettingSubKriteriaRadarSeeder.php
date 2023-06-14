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
        $sub1 = [];
        $sub2 = [];

        for ($i = 1; $i <= 23; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                $sub1[] = [
                    'mr_id' => $j,
                    'msk_id' => $i,
                ];
            }
        }

        for ($i = 24; $i <= 32; $i++) {
            for ($j = 4; $j <= 5; $j++) {
                $sub2[] = [
                    'mr_id' => $j,
                    'msk_id' => $i,
                ];
            }
        }

        SettingSubKriteriaRadar::insert($sub1);
        SettingSubKriteriaRadar::insert($sub2);
    }
}
