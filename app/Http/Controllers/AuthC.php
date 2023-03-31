<?php

namespace App\Http\Controllers;

use App\Models\AppSettings;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthC extends Controller
{
    public AuthService $authService;
    private $__sess_app;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        if (session()->has('app_data')) {
            $oldSessApp = $this->__sess_app;
            $appData = AppSettings::where('is_auto', "Y");
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
            if ($appData->count() > 0) {
                foreach ($appData as $k => $v) {
                    $sessApp[$v->as_key] = !empty($v->as_value) ? $v->as_value : $v->as_default;
                }
            }

            session(['app_data' => $sessApp]);
            $this->__sess_app = session()->get('app_data');
        }
    }

    public function index()
    {
        $data = [
            "__title" => "Login",
            'title_auth' => "Admin",
            // 'title_auth' => $this->__sess_app['title_auth_admin'],
        ];

        return view('auth.v_login', $data);
    }

    public function logout(Request $request)
    {
        $group_id = $request->session()->get("user_data")["group_id"];
        if (in_array($group_id, [1, 2])) {
            $uri = "/auth/admin";
        } else {
            $uri = "/";
        }

        session()->flush();
        return redirect()->to($uri);
    }
}
