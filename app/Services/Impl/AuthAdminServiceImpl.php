<?php

namespace App\Services\Impl;

use App\Models\MsGroups;
use App\Models\MsUsers;
use App\Services\AuthAdminService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthAdminServiceImpl implements AuthAdminService
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
            $quser = MsUsers::where("user_name", $user_name)
                ->where("user_status", true)
                ->whereIn('group_id', [1, 2, 4])
                ->get();

            if ($quser->count() <= 0) {
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

            // $qroute =
            //     "SELECT
            //     mm.menu_link
            // from
            //     group_menus gm 
            // inner join ms_menus mm on
            //     mm.menu_id = gm.menu_id
            // where 
            //     gm.group_id = $user->group_id
            //     and mm.menu_status = true
            // order by
            //     mm.menu_kode
            // ";

            // $droute = DB::select($qroute);
            $droute = MsGroups::find($user->group_id)->menus()->orderBy("menu_kode", "asc")->get();

            if ($droute->count() <= 0) {
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
}
