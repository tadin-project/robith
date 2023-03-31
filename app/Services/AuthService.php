<?php

namespace App\Services;

interface AuthService
{
    function login(string $user_name, string $user_password): array;
}
