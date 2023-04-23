<?php

namespace App\Services\Impl;

use App\Models\AppSettings;
use App\Models\MsKategoriUsaha;
use App\Models\MsUsers;
use App\Models\Tenant;
use App\Services\AuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Summary of AuthServiceImpl
 */
class AuthServiceImpl implements AuthService
{
    /**
     * Summary of __app
     * @var array
     */
    private $__app = [];
    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $appData = AppSettings::all();
        if ($appData->count() > 0) {
            foreach ($appData as $k => $v) {
                $this->__app[$v->as_key] = !empty($v->as_value) ? $v->as_value : $v->as_default;
            }
        }
    }

    /**
     * Summary of getKu
     * @return array
     */
    public function getKu(): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $dt = MsKategoriUsaha::where("mku_status", true)->orderBy("mku_nama", "asc")->get();
            $data = [];
            if ($dt->count() > 0) {
                foreach ($dt as $v) {
                    $data[] = [
                        "mku_id" => $v->mku_id,
                        "mku_nama" => $v->mku_nama,
                    ];
                }
            }
            $res["data"] = $data;
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param string $user_email
     * @param string $user_password
     * @return bool
     */
    public function login(string $user_email, string $user_password): array
    {
        $res = [
            'status' => true,
            'msg' => true,
        ];

        try {
            $quser = DB::select(
                "SELECT
                    mu.*
                from
                    ms_users mu
                where 
                    mu.user_email = '$user_email'
                    and mu.group_id = " . $this->__app["id_tenant"],
            );

            if (count($quser) <= 0) {
                $res['status'] = false;
                $res['msg'] = "Akun tidak dikenal!";
                return $res;
            }

            $user = $quser[0];

            if (!$user->user_status) {
                $res['status'] = false;
                $res['msg'] = "Akun belum diaktifasi. Silahkan lihat email anda untuk aktifasi akun ini!";
                return $res;
            }

            if (!Hash::check($user_password, $user->user_password)) {
                $res['status'] = false;
                $res['msg'] = "Password salah!";

                return $res;
            }

            $qroute =
                "SELECT
                mm.menu_link
            from
                group_menus gm 
            inner join ms_menus mm on
                mm.menu_id = gm.menu_id
            where 
                gm.group_id = $user->group_id
                and mm.menu_status = true
            order by
                mm.menu_kode
            ";

            $droute = DB::select($qroute);

            if (count($droute) <= 0) {
                $res['status'] = false;
                $res['msg'] = "User belum memiliki akses. Silahkan hubungi Admin!";

                return $res;
            }

            $data = [
                "user_id"       => $user->user_id,
                "user_name"     => $user->user_name,
                "user_email"    => $user->user_email,
                "user_fullname" => $user->user_fullname,
                "group_id"      => $user->group_id,
            ];

            $res['data'] = $data;
            $res['route'] = $droute[0]->menu_link;
        } catch (\Exception $e) {
            $res = [
                'status' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * Summary of validasiRegister
     * @param mixed $request
     * @return array
     */
    public function validasiRegister($request): array
    {

        $res = [
            'status' => true,
            'msg' => true,
        ];

        try {
            $userEmail = gettype($request) == "array" ? $request["user_email"] : $request->user_email;
            $userPassword = gettype($request) == "array" ? $request["user_password"] : $request->user_password;
            $mkuId = gettype($request) == "array" ? $request["mku_id"] : $request->mku_id;
            $tenantNama = gettype($request) == "array" ? $request["tenant_nama"] : $request->tenant_nama;
            $tenantDesc = gettype($request) == "array" ? $request["tenant_desc"] : $request->tenant_desc;

            if (empty($tenantNama) || empty($tenantDesc) || empty($userEmail) || empty($userPassword) || empty($mkuId)) {
                $res = [
                    'status' => false,
                    'msg' => "Nama, deskripsi usaha, email, dan password tidak boleh kosong!",
                ];

                return $res;
            }

            if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                $res = [
                    'status' => false,
                    'msg' => "Format email salah!",
                ];

                return $res;
            }

            $cekEmail = MsUsers::where("user_email", $userEmail);
            if ($cekEmail->count() > 0) { // jika email sudah digunakan
                $res = [
                    'status' => false,
                    'msg' => "Email sudah digunakan. Gunakan email yang lain!",
                ];

                return $res;
            }
        } catch (\Exception $e) {
            $res = [
                'status' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * Summary of register
     * @param array $user_data
     * @param array $tenant_data
     * @return array
     */
    function register(array $user_data, array $tenant_data): array
    {

        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            $user = MsUsers::create($user_data);
            $tenant_data["user_id"] = $user->user_id;
            Tenant::create($tenant_data);
        } catch (\Exception $e) {
            $res = [
                'status' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * Summary of aktifasiAkun
     * @param string $token
     * @return array
     */
    public function aktifasiAkun(string $token): array
    {

        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            // cek token
            $user = MsUsers::where("register_token", $token);
            if ($user->count() <= 0) {
                $res = [
                    'status' => false,
                    'msg' => "Token invalid",
                ];
                return $res;
            }

            $user = $user->first();
            $user->user_status = true;
            $user_id = $user->user_id;
            $user->save();

            $tenant = Tenant::where("user_id", $user_id)->first();
            $tenant->tenant_status = true;
            $tenant->save();
            $res["msg"] = "Akun sudah diaktifkan. Silahkan Login";
        } catch (\Exception $e) {
            $res = [
                'status' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return $res;
    }
}
