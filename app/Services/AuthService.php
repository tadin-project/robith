<?php

namespace App\Services;

interface AuthService
{
    function getKu(): array;
    function login(string $user_email, string $user_password): array;
    function validasiRegister($request): array;
    function register(array $user_data, array $tenant_data): array;
    function aktifasiAkun(string $token): array;
}
