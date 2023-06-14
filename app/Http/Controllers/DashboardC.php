<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

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
            $dataAsesmen = [];

            $cekInitData = $this->dashboardService->getInitDataTenant();
            if ($cekInitData["status"]) {
                $dataInit = $cekInitData["data"];
            }

            $cekDataAsesmen = $this->dashboardService->cekAsesmen($this->__sess_user["user_id"]);
            if ($cekDataAsesmen["status"]) {
                $dataAsesmen = $cekDataAsesmen["data"];
            }

            $data["data"] = $dataInit;
            $data["dataAsesmen"] = $dataAsesmen;

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
