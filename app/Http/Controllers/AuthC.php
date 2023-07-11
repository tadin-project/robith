<?php

namespace App\Http\Controllers;

use App\Mail\TenantMail;
use App\Models\AppSettings;
use App\Models\MsUsers;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthC extends Controller
{
    public AuthService $authService;
    private $__sess_app;
    private String $dirLogo = "assets/img/logo";
    private String $dirBackground = "assets/img/background";

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
            'title_auth' => $this->__sess_app["app_nama"],
            'background_auth' => !empty($this->__sess_app["background_auth"]) ? asset('') . $this->dirBackground . "/" . $this->__sess_app["background_auth"] : "",
            'app_logo' => !empty($this->__sess_app["app_logo"]) ? asset('') . $this->dirLogo . "/" . $this->__sess_app["app_logo"] : "",
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
            'title_auth' => $this->__sess_app["app_nama"],
            'background_auth' => !empty($this->__sess_app["background_auth"]) ? asset('') . $this->dirBackground . "/" . $this->__sess_app["background_auth"] : "",
            'app_logo' => !empty($this->__sess_app["app_logo"]) ? asset('') . $this->dirLogo . "/" . $this->__sess_app["app_logo"] : "",
            // 'title_auth' => $this->__sess_app['title_auth_admin'],
            'opt_ku' => $optKu,
        ];

        return view('auth.v_register', $data);
    }

    public function forgot()
    {
        $data = [
            "__title" => "Lupa Password",
            'title_auth' => $this->__sess_app["app_nama"],
            'background_auth' => !empty($this->__sess_app["background_auth"]) ? asset('') . $this->dirBackground . "/" . $this->__sess_app["background_auth"] : "",
            'app_logo' => !empty($this->__sess_app["app_logo"]) ? asset('') . $this->dirLogo . "/" . $this->__sess_app["app_logo"] : "",
        ];

        return view('auth.v_forgot', $data);
    }

    public function reset(Request $request)
    {
        $token = $request->token;
        if (empty($token)) {
            return redirect()->to(route("auth.index"))->with(["error" => "Token invalid"]);
        }

        $cekToken = $this->authService->cekTokenPasswordReset($token, $this->__sess_app["duration_token_reset_password"]);
        if (!$cekToken["status"]) {
            return redirect()->to(route("auth.index"))->with(["error" => $cekToken["msg"]]);
        }

        $data = [
            "__title" => "Lupa Password",
            'title_auth' => $this->__sess_app["app_nama"],
            'token' => $token,
            'background_auth' => !empty($this->__sess_app["background_auth"]) ? asset('') . $this->dirBackground . "/" . $this->__sess_app["background_auth"] : "",
            'app_logo' => !empty($this->__sess_app["app_logo"]) ? asset('') . $this->dirLogo . "/" . $this->__sess_app["app_logo"] : "",
        ];

        return view('auth.v_reset', $data);
    }

    public function logout(Request $request)
    {
        $group_id = $request->session()->get("user_data")["group_id"];
        if (in_array($group_id, [1, 2, 4])) {
            $uri = "/admin";
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

        try {
            $cekInput = $this->authService->validasiRegister($request);
            if (!$cekInput["status"]) {
                $res = $cekInput;
                return response()->json($res);
            }

            $token = Str::random(80);

            $user_data = [
                "user_name" => $request->user_email,
                "user_email" => $request->user_email,
                "user_password" => Hash::make($request->user_password),
                "user_status" => false,
                "group_id" => $this->__sess_app["id_tenant"],
                "register_token" => $token,
            ];

            $tenant_data = [
                "tenant_nama" => $request->tenant_nama,
                "tenant_desc" => $request->tenant_desc,
                "tenant_status" => false,
                "mku_id" => $request->mku_id,
            ];

            $res = $this->authService->register($user_data, $tenant_data);

            $details = [
                '__title' => $this->__sess_app["app_nama"],
                'title' => 'Mail from EFQM',
                'body' => 'Terima kasih telah mendaftar ke platform kami. Untuk langkah selanjutnya, silahkan Anda aktifasi akun anda melalui link berikut : <br><br><br>
                <a href="https://businessperformancetoolswmkpbltppns.site/activate?token=' . $token . '">Link Aktifasi Akun</a>
                ',
            ];

            Mail::to($request->user_email)->send(new TenantMail($details));
        } catch (\Throwable $th) {
            MsUsers::where("user_email", $request->user_email)->delete();
            $res = [
                "status" => false,
                "message" => $th->getMessage(),
            ];
        }

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

    public function prosesForgot(Request $request): JsonResponse
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        $cekUser = $this->authService->cekEmail($this->__sess_app["id_tenant"], $request->user_email);
        if (!$cekUser["status"]) {
            return response()->json($cekUser);
        }

        $res = $cekUser;

        $details = [
            '__title' => $this->__sess_app["app_nama"],
            'title' => 'Mail from EFQM',
            'body' => 'Ini adalah email untuk reset password. Jangan bagikan email ini kepada siapapun. Silahkan klik link berikut untuk proses selanjutnya: <br><br><br>
            <a href="http://127.0.0.1/robith/public/reset?token=' . $res["data"] . '">Link Reset Password</a>
            ',
        ];

        Mail::to($request->user_email)->send(new TenantMail($details));
        return response()->json($res);
    }

    public function prosesReset(Request $request): JsonResponse
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        $res = $this->authService->updatePass($request->token, $request->user_pass);
        return response()->json($res);
    }
}
