<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tenant::create([
            'tenant_nama' => 'Tenant User 02',
            'tenant_desc' => 'Tes',
            'tenant_status' => true,
            'user_id' => 4,
            'mku_id' => 6,
        ]);
    }
}
