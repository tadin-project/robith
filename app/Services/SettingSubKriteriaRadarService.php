<?php

namespace App\Services;

interface SettingSubKriteriaRadarService
{
    public function getTotal(string $where = ""): array;
    public function getData(string $where = "", string $order = "", string $limit = "", array $cols = []): array;
    public function getDimensi(): array;
    public function getKriteria(string $md_id): array;
    public function getSubKriteria(string $mk_id): array;
    public function add(array $data): array;
    public function del(string $msk_id, array $mr_id): array;
}
