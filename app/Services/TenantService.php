<?php

namespace App\Services;

interface TenantService
{
    public function getUsers(string $act = "add", string $oldUser = "0"): array;
    public function getKategoriUsaha(): array;
    public function getTotal(string $where): array;
    public function getData(string $where = "", string $order = "", string $limit = "", array $cols = []): array;
    public function validateData($req): array;
    public function add(array $data): array;
    public function edit(string $id, array $data): array;
    public function del(string $id): array;
    public function getById(string $id): array;
}
