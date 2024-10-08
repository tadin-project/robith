<?php

namespace App\Http\Controllers;

use App\Services\oldAsesmenService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class oldAsesmenC extends MyC
{
    private oldAsesmenService $oldAsesmenService;
    private string $dirUploads = "uploads/lampiran_submission";
    public function __construct(oldAsesmenService $oldAsesmenService)
    {
        parent::__construct();
        $this->middleware("has_akses:asesmen");
        $this->oldAsesmenService = $oldAsesmenService;
    }

    public function index(): View
    {
        $dtKriteria = [];
        $cekKriteria = $this->oldAsesmenService->getKriteria();
        if ($cekKriteria["status"]) {
            $dtKriteria = $cekKriteria["data"];
        }

        $data = [
            "__title" => "Asesmen",
            "dtKriteria" => $dtKriteria,
            "dirUploads" => $this->dirUploads,
        ];

        return $this->my_view("v_asesmen", $data);
    }

    public function cekData(): JsonResponse
    {
        // cek apakah user memiliki tenant
        $cekTenant = $this->oldAsesmenService->getTenantByUser($this->__sess_user["user_id"]);
        if ($cekTenant["data"] == 0) {
            return response()->json($cekTenant);
        }

        $tenant_id = $cekTenant["data"];
        $res = $this->oldAsesmenService->getById($tenant_id);
        return response()->json($res);
    }

    public function saveSementara(Request $request) : JsonResponse
    {
        $msk_id = $request->msk_id;
        $msk_value = $request->msk_value;
        $asd_id = $request->id_detail;
        $list_hapus_lampiran = $request->list_hapus_lampiran;

        if (empty($msk_id)) $msk_id = [];
        if (empty($msk_value)) $msk_value = [];
        if (empty($asd_id)) $asd_id = [];
        if (empty($list_hapus_lampiran)) $list_hapus_lampiran = "";
        $list_hapus_lampiran = explode(",", $list_hapus_lampiran);
        if (count($list_hapus_lampiran) > 0) {
            $this->oldAsesmenService->hapusLampiran($this->dirUploads, $list_hapus_lampiran);
        }

        $lampiran = [];
        if ($request->file('lampiran')) {
            foreach ($request->file('lampiran') as $k => $f) {
                $fileName = time() . rand(1, 99) . '.' . $f->extension();
                $f->move(public_path($this->dirUploads), $fileName);
                $lampiran[$k] = $fileName;
            }
        }

        if (count($msk_id) <= 0) {
            $res = [
                "status" => false,
                "msg" => "Minimal kirimkan 1 data",
            ];
            return response()->json($res);
        }

        // cek apakah user memiliki tenant
        $cekTenant = $this->oldAsesmenService->getTenantByUser($this->__sess_user["user_id"]);
        if ($cekTenant["data"] == 0) {
            $res = [
                "status" => false,
                "msg" => "Tenant tidak dikenali!",
            ];
            return response()->json($res);
        }

        $tenant_id = $cekTenant["data"];

        $data = [
            "tenant_id" => $tenant_id,
            "user_id" => $this->__sess_user["user_id"],
        ];

        $hasAsesmen = $this->oldAsesmenService->cekAsesmen($tenant_id);

        if (!$hasAsesmen["status"]) {
            return response()->json($hasAsesmen);
        }

        $as_id = 0;
        if ($hasAsesmen["data"] > 0) {
            $as_id = $hasAsesmen["data"];
            $res = $this->oldAsesmenService->edit($as_id, $data);
        } else {
            $res = $this->oldAsesmenService->add($data);
            if ($res["status"]) $as_id = $res["data"];
        }

        if (!$res["status"]) {
            return response()->json($res);
        }

        $detail = [];
        $i = 0;
        foreach ($msk_id as $k => $v) {
            $detail[$i] = [
                "msk_id" => $v,
                "asd_value" => $msk_value[$k],
                "as_id" => $as_id,
            ];


            if ($hasAsesmen["data"] > 0) {
                $detail[$i]["asd_id"] = $asd_id[$k];
                $dt_detail = $this->oldAsesmenService->getDetailById($asd_id[$k]);

                if (count($dt_detail) > 0) {
                    $detail[$i]["asd_file"] = $dt_detail["asd_file"];
                } else {
                    $detail[$i]["asd_file"] = null;
                }

                if (array_key_exists($k, $lampiran)) {
                    if (!empty($dt_detail["asd_file"])) {
                        $this->oldAsesmenService->hapusFile("./" . $this->dirUploads . "/" . $dt_detail["asd_file"]);
                    }
                    $detail[$i]["asd_file"] = $lampiran[$k];
                }
            } else {
                $detail[$i]["asd_file"] = null;
                if (array_key_exists($k, $lampiran)) $detail[$i]["asd_file"] = $lampiran[$k];
            }

            $i++;
        }


        if ($hasAsesmen["data"] > 0) {
            $res = $this->oldAsesmenService->editDetail($detail);
        } else {
            $res = $this->oldAsesmenService->addDetail($detail);
        }

        return response()->json($res);
    }

    public function save()
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        // cek apakah user memiliki tenant
        $cekTenant = $this->oldAsesmenService->getTenantByUser($this->__sess_user["user_id"]);
        if ($cekTenant["data"] == 0) {
            $res = [
                "status" => false,
                "msg" => "Tenant tidak dikenali!",
            ];
            return response()->json($res);
        }

        $tenant_id = $cekTenant["data"];

        $data = [
            "as_status" => 1,
        ];

        $hasAsesmen = $this->oldAsesmenService->cekAsesmen($tenant_id);

        if (!$hasAsesmen["status"]) {
            return response()->json($hasAsesmen);
        }

        $as_id = $hasAsesmen["data"];
        $res = $this->oldAsesmenService->edit($as_id, $data);

        return response()->json($res);
    }
}
