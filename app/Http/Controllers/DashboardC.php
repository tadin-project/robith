<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardC extends MyC
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        parent::__construct();
        $this->dashboardService = $dashboardService;
        $this->middleware("has_akses:dashboard");
    }

    public function index(): View
    {
        $data = [
            "__title" => "Dashboard",
            "user" => $this->__sess_user,
        ];

        if ($this->__sess_user["group_id"] == 3) {
            $dataInit = [];
            $cekInitData = $this->dashboardService->getInitDataTenant();
            if ($cekInitData["status"]) {
                $dataInit = $cekInitData["data"];
            }
            $data["data"] = $dataInit;
            return $this->my_view("v_dashboard_tenant", $data);
        } else {
            return $this->my_view("v_dashboard_default", $data);
        }
    }

    public function getDataTenant(): JsonResponse
    {
        $res = $this->dashboardService->getDataTenant($this->__sess_user["user_id"]);
        return response()->json($res);
    }
}
