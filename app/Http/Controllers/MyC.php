<?php

namespace App\Http\Controllers;

use App\Models\AppSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MyC extends Controller
{
    protected $__sess_app;
    protected $__sess_user;
    private String $dirLogo = "assets/img/logo";

    public function __construct()
    {
        if (session()->has('app_data')) {
            $oldSessApp = $this->__sess_app;
            $appData = AppSettings::where('is_auto', 'Y');
            if ($appData->count() > 0) {
                foreach ($appData->get() as $k => $v) {
                    $oldSessApp[$v->as_key] = !empty($v->as_value) ? $v->as_value : $v->as_default;
                }

                session(['app_data' => $oldSessApp]);
                $this->__sess_app = $oldSessApp;
            }
        } else {
            $sessApp = [];
            $appData = AppSettings::all();
            if (count($appData) > 0) {
                foreach ($appData as $k => $v) {
                    $sessApp[$v->as_key] = !empty($v->as_value) ? $v->as_value : $v->as_default;
                }
            }

            session(['app_data' => $sessApp]);
            $this->__sess_app = session()->get('app_data');
        }

        $this->middleware(function ($request, $next) {
            $this->__sess_user = $request->session()->get("user_data");
            return $next($request);
        });
    }

    public function my_view($filename, array $data = [])
    {
        $__title = $this->__sess_app['app_nama'];
        if (key_exists("__title", $data)) {
            $__title .= " | " . $data["__title"];
        }

        if (array_key_exists("logo_icon_admin", $this->__sess_app)) {
            $this->__sess_app["logo_icon_admin"] = asset('') . $this->dirLogo . '/' . $this->__sess_app["logo_icon_admin"];
        }

        $param_data = [
            "__title" => $__title,
            '__sidebar' => $this->getSideBar($this->__sess_user['group_id']),
            '__view' => view($filename, $data),
            '__user' => $this->__sess_user,
            '__sess_app' => $this->__sess_app,
        ];

        return view('template.v_master_tmp', $param_data);
    }

    public function getSideBar(int $group_id, int $parent_menu_id = 0): string
    {
        $res = "";
        $where = "";

        $where .= " AND mm.parent_menu_id = $parent_menu_id ";

        $sql = "SELECT
                    distinct mm.*,
                    coalesce(ch.tot_child, 0) as tot_child
                from
                    ms_menus mm
                inner join group_menus gm on
                    gm.menu_id = mm.menu_id
                left join (
                    select
                        count(*) as tot_child,
                        parent_menu_id 
                    from
                        ms_menus mm
                    group by
                        mm.parent_menu_id) ch on
                    ch.parent_menu_id = mm.menu_id
                where
                    mm.menu_status = 1
                    and gm.group_id = $group_id
                    $where
                order by
                    mm.menu_kode";

        $dt_sidebar = DB::select($sql);

        // dd($dt_sidebar);

        $link = url('');
        if (count($dt_sidebar) > 0) {
            foreach ($dt_sidebar as $v) {
                if ($v->menu_type == 2) {
                    $res .= "<li class=\"nav-header\">$v->menu_nama</li>";
                    if ($v->tot_child > 0) {
                        $res .= $this->getSideBar($group_id, $v->menu_id);
                    }
                } else {
                    if ($v->tot_child > 0) {
                        $res .= "<li class=\"nav-item\">
                                    <a href=\"#\" class=\"nav-link\">
                                        <i class=\"" . (!empty($v->menu_ikon) ? $v->menu_ikon : "far fa-circle") . " nav-icon\"></i>
                                        <p>
                                            $v->menu_nama
                                            <i class=\"right fas fa-angle-left\"></i>
                                        </p>
                                    </a>
                                    <ul class=\"nav nav-treeview\">";
                        $res .= $this->getSideBar($group_id, $v->menu_id);
                        $res .= "</ul>
                                    </li>";
                    } else {
                        $res .= "<li class=\"nav-item\">
                                    <a href=\"" . $link . "/" . $v->menu_link . "\" class=\"nav-link\">
                                    <i class=\"" . (!empty($v->menu_ikon) ? $v->menu_ikon : "far fa-circle") . " nav-icon\"></i>
                                    <p>$v->menu_nama</p>
                                    </a>
                                </li>";
                        // dd($dt_sidebar);
                    }
                }
            }
        }
        return $res;
    }
}
