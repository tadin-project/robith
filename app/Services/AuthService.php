<?php

namespace App\Services;

interface AuthService
{
    function getKu(): array;
    function login(string $user_email, string $user_password): array;
    function validasiRegister($request): array;
    function register(array $user_data, array $tenant_data): array;
    function aktifasiAkun(string $token): array;
    function cekEmail(string $group_id, string $email): array;
    function cekTokenPasswordReset(string $token, string $durationToken): array;
    function updatePass(string $token, string $user_pass): array;
}
