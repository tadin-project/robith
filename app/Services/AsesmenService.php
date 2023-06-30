<?php

namespace App\Services;

interface AsesmenService
{
    public function getIntroduction(): array;
    public function getKriteria(): array;
    public function getSubKriteria(): array;
    public function getConvertionValue(): array;
    public function getTenantByUser(string $id): array;
    public function getById(string $tenant_id): array;
    public function cekAsesmen(string $tenant_id): array;
    public function add(array $data): array;
    public function edit(string $id, array $data): array;
    public function addDetail(array $data): array;
    public function editDetail(array $data): array;
    public function getFileById(string $asd_id): array;
    public function hapusLampiran(string $dir, array $list_id_asd): array;
    public function hapusFile(string $file): void;
    public function cekHasComplete(string $tenant_id): array;
    // public function editNonSubmission(string $as_id): array;
    public function addFile(array $data): array;
    public function editFile(array $data): array;
}
