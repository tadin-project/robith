<?php

namespace App\Services;

interface ProfilService
{
    public function getKategoriUsaha(): array;
    public function getUserData(string $user_id): array;
    public function saveUser(string $user_id, array $data): array;
    public function saveTenant(string $user_id, array $data): array;
    public function cekTenant(string $user_id): array;
    public function validasiPassLama(array $data): array;
}
