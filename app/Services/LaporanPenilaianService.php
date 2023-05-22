<?php

namespace App\Services;

interface LaporanPenilaianService
{
    public function getTotal(string $where): array;
    public function getData(string $where = "", string $order = "", string $limit = "", array $cols = []): array;
    public function getById($id): array;
}
