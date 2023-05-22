<?php

namespace App\Services;

interface LaporanPenilaianUserService
{
    public function getKriteriaData(string $user_id): array;
    public function cetak(string $user_id): array;
}
