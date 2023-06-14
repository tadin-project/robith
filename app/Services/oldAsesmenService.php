<?php

namespace App\Services;

interface oldAsesmenService
{
    public function getKriteria(): array;
    public function getTenantByUser(string $id): array;
    public function getById(string $tenant_id): array;
    public function cekAsesmen(string $tenant_id): array;
    public function add(array $data): array;
    public function edit(string $id, array $data): array;
    public function addDetail(array $data): array;
    public function editDetail(array $data): array;
    public function getDetailById(string $asd_id): array;
    public function hapusLampiran(string $dir, array $list_id_asd): array;
    public function hapusFile(string $file): void;
}
