<?php

namespace App\Http\Controllers;

use App\Services\ProfilService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfilC extends MyC
{
    private ProfilService $profilService;
    public function __construct(ProfilService $profilService)
    {
        parent::__construct();
        $this->profilService = $profilService;
    }

    public function index(): View
    {
        $cekUser = $this->profilService->getUserData($this->__sess_user["user_id"]);
        $userData = [];
        if ($cekUser["status"]) {
            $userData = $cekUser["data"];
        }

        $cekKategoriUsaha = $this->profilService->getKategoriUsaha();
        $optionKategoriUsaha = [];
        if ($cekKategoriUsaha["status"]) {
            $optionKategoriUsaha = $cekKategoriUsaha["data"];
        }

        $data = [
            "__title" => "Profil " . (!empty($userData["user_fullname"]) ? $userData["user_fullname"] : $userData["user_name"]),
            "kategori_usaha" => $optionKategoriUsaha,
            "is_tenant" => count($userData["tenant"]) > 0 ? true : false,
            "user_data" => $userData,
        ];

        return $this->my_view("v_profil", $data);
    }

    public function saveProfil(Request $request): JsonResponse
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        $user = [
            "user_fullname" => $request->user_fullname,
        ];

        $tenant = [
            "tenant_nama" => $request->tenant_nama,
            "tenant_desc" => $request->tenant_desc,
            "mku_id" => $request->mku_id,
        ];

        $save_user = $this->profilService->saveUser($this->__sess_user["user_id"], $user);
        if (!$save_user["status"]) {
            return response()->json($save_user);
        }

        // menyimpan data file yang diupload
        $user_profile = $request->file('user_profile');

        $user_profile_name = "";
        if ($request->file('user_profile')) {
            if ($user_profile->isValid()) {
                $extension = $user_profile->extension();
                $user_profile_name = $this->__sess_user["user_id"] . "." . $extension;
                $user_profile->move(public_path('uploads/profile'), $user_profile_name);

                $user["user_profile"] = $user_profile_name;
                $save_user = $this->profilService->saveUser($this->__sess_user["user_id"], $user);
                if (!$save_user["status"]) {
                    return response()->json($save_user);
                }
            }
        }

        if ($this->__sess_user["group_id"] == 3) {
            $save_tenant = $this->profilService->saveTenant($this->__sess_user["user_id"], $tenant);
            if (!$save_tenant["status"]) {
                return response()->json($save_tenant);
            }
        }

        $old_sess_user = $this->__sess_user;
        $old_sess_user["user_fullname"] = $user["user_fullname"];
        if (!empty($user_profile_name)) {
            $old_sess_user["user_profile"] = $user_profile_name;
        }
        session(["user_data" => $old_sess_user]);

        return response()->json($res);
    }

    public function savePass(Request $request)
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        $user = [
            "user_password" => Hash::make($request->user_pass),
        ];

        $cekValidasiPassLama = $this->profilService->validasiPassLama([
            "old_pass" => $request->old_user_pass,
            "user_id" => $this->__sess_user["user_id"],
        ]);
        if (!$cekValidasiPassLama["status"]) {
            return response()->json($cekValidasiPassLama);
        }

        $save_user = $this->profilService->saveUser($this->__sess_user["user_id"], $user);
        if (!$save_user["status"]) {
            return response()->json($save_user);
        }

        return response()->json($res);
    }
}
