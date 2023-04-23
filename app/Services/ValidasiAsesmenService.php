<?php

namespace App\Services;

interface ValidasiAsesmenService
{
    public function getKategoriUsaha(): array;
    public function getTotal(string $where): array;
    public function getData(string $where, string $order, string $limit, array $columns): array;
    public function getKriteria(): array;
    public function edit(string $id): array;
    public function update(string $id, array $data): array;
    public function updateDetail(string $id, array $data): array;
}
