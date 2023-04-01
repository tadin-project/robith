<?php

namespace App\Services;

interface MsKategoriService
{
    public function getTotal(string $where): array;
    public function getData(string $where = "", string $order = "", string $limit = "", array $cols = []): array;
    public function validateData($req): array;
    public function add(array $data): array;
    public function edit($id, array $data): array;
    public function del($id): array;
    public function checkDuplicate(string $act, string $key, string $val, string $old = ""): string;
    public function getById($id): array;
}
