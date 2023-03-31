<?php

namespace App\Services;

interface AuthAdminService
{
    function login(string $user_name, string $user_password): array;
}
