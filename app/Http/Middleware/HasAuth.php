<?php

namespace App\Http\Middleware;

use App\Models\MsGroups;
use Closure;
use Illuminate\Http\Request;

class HasAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $listRouteAuth = [
            "/",
            "admin",
            "login",
            "register",
            "forgot",
            "reset",
        ];

        if (in_array($request->path(), $listRouteAuth)) { // redirect halaman dashboard jika punya session user_data
            // dd("disini");
            if ($request->session()->has("user_data") && $request->session()->get("user_data")) {
                $group_id = $request->session()->get("user_data")["group_id"];
                $urlRedirect = MsGroups::find($group_id)->menus()->orderBy("menu_kode", "asc")->first()->menu_link;
                if ($request->ajax()) {
                    return response(json_encode(["status" => true, "" => $urlRedirect]));
                } else {
                    return redirect()->to($urlRedirect);
                }
            }
        } else { // redirect ke halaman login jika tidak punya session user_data
            if (!$request->session()->has("user_data") || !$request->session()->get("user_data")) {
                return redirect()->to('/');
            }
        }
        return $next($request);
        // dd($request->session()->all());
    }
}
