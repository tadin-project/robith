<?php

namespace App\Services;

interface AsesmenService
{
    public function getKriteria(): array;
    public function getSubKriteria(string $mk_id): array;
}
