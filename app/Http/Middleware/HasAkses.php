<?php

namespace App\Http\Middleware;

use App\Models\MsGroups;
use Closure;
use Illuminate\Http\Request;

class HasAkses
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $url)
    {
        $group_id = $request->session()->get("user_data")["group_id"];
        $cekAkses = MsGroups::find($group_id)->menus()->where("menu_link", $url)->get();
        if ($cekAkses->count() <= 0) {
            return abort(403);
        }
        return $next($request);
    }
}
