<?php

namespace App\Services;

interface AppSettingsService
{
    public function getTotal(string $where): array;
    public function getData(string $where = "", string $order = "", string $limit = "", array $cols = []): array;
    public function validateData($req): array;
    public function edit($id, array $data): array;
    public function getById($id): array;
}
