<?php

namespace Database\Seeders;

use App\Models\GroupMenus;
use Illuminate\Database\Seeder;

class GroupMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];

        for ($i = 1; $i <= 4; $i++) {
            for ($j = 1; $j <= 27; $j++) {
                if ($i == 1) {
                    if ($j == 16 || $j == 17 || $j == 22) {
                        continue;
                    }
                } else if ($i == 2) {
                    if ($j == 5 || $j == 16 || $j == 17 || $j == 22) {
                        continue;
                    }
                } else if ($i == 3) {
                    if (($j >= 1 && $j <= 6) || ($j >= 8 && $j <= 15) || $j == 18 || $j == 19 || $j == 21 || ($j >= 25 && $j <= 27)) {
                        continue;
                    }
                } else if ($i == 4) {
                    if (($j >= 1 && $j <= 6) || ($j >= 8 && $j <= 17) || ($j >= 20 && $j <= 22) || ($j >= 25 && $j <= 27)) {
                        continue;
                    }
                }

                $data[] = [
                    "group_id" => $i,
                    "menu_id" => $j,
                ];
            }
        }

        GroupMenus::insert($data);
    }
}
