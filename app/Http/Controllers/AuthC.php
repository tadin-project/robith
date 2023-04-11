<?php

namespace App\Http\Controllers;

use App\Models\AppSettings;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

    public function register()
    {
        $cekOptKu = $this->authService->getKu();
        $optKu = [];
        if ($cekOptKu["status"]) {
            $optKu = $cekOptKu["data"];
        }

        $data = [
            "__title" => "Register",
            'title_auth' => "Admin",
            // 'title_auth' => $this->__sess_app['title_auth_admin'],
            'opt_ku' => $optKu,
        ];

        return view('auth.v_register', $data);
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

    public function prosesLogin(Request $request): JsonResponse
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        $cekUser = $this->authService->login($request->user_email, $request->user_password);
        if (!$cekUser["status"]) {
            return response()->json($cekUser);
        }

        session(["user_data" => $cekUser["data"]]);
        $res["route"] = $cekUser["route"];

        return response()->json($res);
    }

    public function prosesRegister(Request $request): JsonResponse
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        $cekInput = $this->authService->validasiRegister($request);
        if (!$cekInput["status"]) {
            $res = $cekInput;
            return response()->json($res);
        }

        $user_data = [
            "user_name" => $request->user_email,
            "user_email" => $request->user_email,
            "user_password" => Hash::make($request->user_password),
            "user_status" => false,
            "group_id" => $this->__sess_app["id_tenant"],
            "register_token" => Str::random(80),
        ];

        $tenant_data = [
            "tenant_nama" => $request->tenant_nama,
            "tenant_desc" => $request->tenant_desc,
            "tenant_status" => false,
            "mku_id" => $request->mku_id,
        ];

        $res = $this->authService->register($user_data, $tenant_data);

        return response()->json($res);
    }

    public function aktifasiAkun(Request $request)
    {
        $token = $request->token;
        if (empty($token)) $token = "";
        $res = $this->authService->aktifasiAkun($token);
        if (!$res["status"]) {
            return redirect()->to(route("auth.index"))->with(["error" => $res["msg"]]);
        } else {
            return redirect()->to(route("auth.index"))->with(["success" => $res["msg"]]);
        }
    }
}
