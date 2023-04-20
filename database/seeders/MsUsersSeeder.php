<?php

namespace Database\Seeders;

use App\Models\MsUsers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MsUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MsUsers::create([
            'user_id' => 1,
            'user_name' => 'root',
            'user_email' => 'root@gmail.com',
            'user_password' => Hash::make('root123'),
            'user_fullname' => 'Root',
            'user_status' => true,
            'group_id' => 1,
        ]);

        MsUsers::create([
            'user_id' => 2,
            'user_name' => 'admin',
            'user_email' => 'admin@gmail.com',
            'user_password' => Hash::make('admin123'),
            'user_fullname' => 'Administrator',
            'user_status' => true,
            'group_id' => 2,
        ]);

        MsUsers::create([
            'user_id' => 3,
            'user_name' => 'user01',
            'user_email' => 'user01@gmail.com',
            'user_password' => Hash::make('user01'),
            'user_fullname' => 'User 01',
            'user_status' => true,
            'group_id' => 3,
        ]);

        MsUsers::create([
            'user_id' => 4,
            'user_name' => 'user02',
            'user_email' => 'user02@gmail.com',
            'user_password' => Hash::make('user02'),
            'user_fullname' => 'User 02',
            'user_status' => true,
            'group_id' => 3,
        ]);

        MsUsers::create([
            'user_id' => 5,
            'user_name' => 'validator01',
            'user_email' => 'validator01@gmail.com',
            'user_password' => Hash::make('validator01'),
            'user_fullname' => 'Validator 01',
            'user_status' => true,
            'group_id' => 4,
        ]);
    }
}
