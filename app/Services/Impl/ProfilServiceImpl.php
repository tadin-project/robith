<?php

namespace App\Services\Impl;

use App\Models\MsKategoriUsaha;
use App\Models\MsUsers;
use App\Models\Tenant;
use App\Services\ProfilService;
use Illuminate\Support\Facades\Hash;

class ProfilServiceImpl implements ProfilService
{
    public function getKategoriUsaha(): array
    {

        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $res["data"] = MsKategoriUsaha::where("mku_status", true)->orderBy("mku_nama", "asc")->get();
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    public function getUserData(string $user_id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $user = MsUsers::find($user_id);
            if (!isset($user->user_id)) {
                $res = [
                    "status" => false,
                    "msg" => "Data tidak ditemukan! Silahkan relogin",
                ];
                return $res;
            }

            $user_data = [
                "user_name" => $user->user_name,
                "user_email" => $user->user_email,
                "user_fullname" => $user->user_fullname,
                "group_id" => $user->group_id,
                "tenant" => [],
            ];

            if ($user_data["group_id"] == 3) {
                $user_data["tenant"] = [
                    "tenant_nama" => $user->tenant->tenant_nama,
                    "tenant_desc" => $user->tenant->tenant_desc,
                    "mku_id" => $user->tenant->mku_id,
                ];
            }

            $res["data"] = $user_data;
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    public function saveUser(string $user_id, array $data): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            MsUsers::find($user_id)->update($data);
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    public function saveTenant(string $user_id, array $data): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            Tenant::where("user_id", $user_id)->update($data);
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    public function cekTenant(string $user_id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $tenant = Tenant::where("user_id", $user_id);
            if (!isset($tenant->tenant_id)) {
                $res = [
                    "status" => false,
                    "msg" => "Tenant tidak ditemukan!",
                ];
            }
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    public function validasiPassLama(array $data): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $user = MsUsers::find($data["user_id"]);
            if (!isset($user->user_id)) {
                $res = [
                    "status" => false,
                    "msg" => "Data tidak ditemukan!",
                ];

                return $res;
            }

            if (!Hash::check($data["old_pass"], $user->user_password)) {
                $res = [
                    "status" => false,
                    "msg" => "Password lama salah!",
                ];
            }
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }
}
