<?php

namespace App\Http\Controllers;

use App\Models\AppSettings;
use App\Services\AuthAdminService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthAdminC extends Controller
{
    public AuthAdminService $authAdminService;
    private $__sess_app;
    private String $dirLogo = "assets/img/logo";
    private String $dirBackground = "assets/img/background";

    public function __construct(AuthAdminService $authAdminService)
    {
        $this->authAdminService = $authAdminService;
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

    public function index(): View
    {
        $data = [
            "__title" => "Login",
            'title_app' => "Admin",
            'background_auth' => !empty($this->__sess_app["background_auth"]) ? asset('') . $this->dirBackground . "/" . $this->__sess_app["background_auth"] : "",
            'app_logo' => !empty($this->__sess_app["app_logo"]) ? asset('') . $this->dirLogo . "/" . $this->__sess_app["app_logo"] : "",
        ];

        return view('auth.v_login_admin', $data);
    }

    public function login(Request $request): JsonResponse
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        $cekUser = $this->authAdminService->login($request->user_name, $request->user_password);
        if (!$cekUser["status"]) {
            return response()->json($cekUser);
        }

        session(["user_data" => $cekUser["data"]]);
        $res["route"] = $cekUser["route"];

        return response()->json($res);
    }
}
