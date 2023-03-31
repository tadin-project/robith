<?php

namespace App\Services\Impl;

use App\Services\AuthService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthServiceImpl implements AuthService
{

    /**
     * @param string $user_name
     * @param string $user_password
     * @return bool
     */
    public function login(string $user_name, string $user_password): array
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
                    mu.user_name = '$user_name'
                    and mu.user_status = true
                "
            );

            if (count($quser) <= 0) {
                $res['status'] = false;
                $res['msg'] = "Akun tidak dikenal!";
                return $res;
            }

            $user = $quser[0];
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

            $data = new \stdClass();
            $data->user_id       = $user->user_id;
            $data->user_name     = $user->user_name;
            $data->user_email    = $user->user_email;
            $data->user_fullname = $user->user_fullname;
            $data->group_id      = $user->group_id;

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
}
