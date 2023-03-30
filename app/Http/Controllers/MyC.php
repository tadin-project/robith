<?php

namespace App\Http\Controllers;

use App\Models\AppSettings;
use Illuminate\Http\Request;

class MyC extends Controller
{
    protected $__sess_app;
    protected $__sess_user;

    public function __construct()
    {
        if (session()->has('user_data')) {
            $this->__sess_user = session()->get('user_data');
        } else {
            $this->__sess_user = config("constants.session.user_data");
        }

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
    }

    public function my_view($filename, array $data = [])
    {
        $__title = $this->__sess_app['app_nama'];
        if (key_exists("__title", $data)) {
            $__title .= " | " . $data["__title"];
        }

        $param_data = [
            "__title" => $__title,
            // '__sidebar' => $this->getSideBar($this->__sess_user['user_id']),
            '__sidebar' => "",
            '__view' => view($filename, $data),
            '__user' => $this->__sess_user,
            '__sess_app' => $this->__sess_app,
        ];

        return view('template.v_master_tmp', $param_data);
    }
}
