<?php

namespace App\Services;

interface DashboardService
{
    public function getInitDataTenant(): array;
    public function getDataTenant(int $user_id): array;
    public function cekAsesmen(int $user_id): array;
}
