<?php

namespace App\Services;

interface SettingSubKriteriaRadarService
{
    public function getTotal(string $where): array;
    public function getData(string $where = "", string $order = "", string $limit = "", array $cols = []): array;
    public function add(array $data): array;
    public function del($id): array;
}
