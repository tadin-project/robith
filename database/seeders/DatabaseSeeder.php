<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(MsGroupsSeeder::class);
        $this->call(MsUsersSeeder::class);
        $this->call(MsMenusSeeder::class);
        $this->call(GroupMenusSeeder::class);
        $this->call(AppSettingsSeeder::class);
        $this->call(MsDimensiSeeder::class);
        $this->call(MsKriteriaSeeder::class);
        $this->call(MsSubKriteriaSeeder::class);
        $this->call(MsKategoriUsahaSeeder::class);
    }
}
