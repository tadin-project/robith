<?php

namespace App\Http\Controllers;

use App\Services\AsesmenService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsesmenC extends MyC
{
    private AsesmenService $asesmenService;
    private $dirUploads = "uploads/lampiran_submission";
    public function __construct(AsesmenService $asesmenService)
    {
        parent::__construct();
        $this->middleware("has_akses:asesmen");
        $this->asesmenService = $asesmenService;
    }

    public function index(): View
    {
        $dtKriteria = [];
        $dtSubKriteria = [];
        $dtConvertionValue = [];

        $cekKriteria = $this->asesmenService->getKriteria();
        if ($cekKriteria["status"]) {
            $dtKriteria = $cekKriteria["data"];
        }

        $cekSubKriteria = $this->asesmenService->getSubKriteria();
        if ($cekSubKriteria["status"]) {
            $dtSubKriteria = $cekSubKriteria["data"];
        }

        $cekConvertionValue = $this->asesmenService->getConvertionValue();
        if ($cekConvertionValue["status"]) {
            $dtConvertionValue = $cekConvertionValue["data"];
        }

        $data = [
            "__title" => "Asesmen",
            "dtKriteria" => $dtKriteria,
            "dtSubKriteria" => $dtSubKriteria,
            "dtConvertionValue" => $dtConvertionValue,
            "dirUploads" => $this->dirUploads,
        ];

        return $this->my_view("v_asesmen", $data);
    }

    public function cekData(): JsonResponse
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        // cek apakah user memiliki tenant
        $cekTenant = $this->asesmenService->getTenantByUser($this->__sess_user["user_id"]);
        if ($cekTenant["data"] == 0) {
            return response()->json($cekTenant);
        }

        $tenant_id = $cekTenant["data"];
        $res = $this->asesmenService->getById($tenant_id);
        return response()->json($res);
    }

    public function saveSementara(Request $request)
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        $msk_id = $request->msk_id;
        $asd_value = $request->asd_value;
        $asd_id = $request->asd_id;
        $list_hapus_lampiran = $request->list_hapus_lampiran;

        if (empty($msk_id)) $msk_id = [];
        if (empty($asd_value)) $asd_value = [];
        if (empty($asd_id)) $asd_id = [];
        if (empty($list_hapus_lampiran)) $list_hapus_lampiran = "";
        $list_hapus_lampiran = explode(",", $list_hapus_lampiran);
        if (count($list_hapus_lampiran) > 0) {
            $this->asesmenService->hapusLampiran($this->dirUploads, $list_hapus_lampiran);
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
        $cekTenant = $this->asesmenService->getTenantByUser($this->__sess_user["user_id"]);
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

        $hasAsesmen = $this->asesmenService->cekAsesmen($tenant_id);

        if (!$hasAsesmen["status"]) {
            return response()->json($hasAsesmen);
        }

        $as_id = 0;
        if ($hasAsesmen["data"] > 0) {
            $as_id = $hasAsesmen["data"];
            $res = $this->asesmenService->edit($as_id, $data);
        } else {
            $res = $this->asesmenService->add($data);
            if ($res["status"]) $as_id = $res["data"];
        }

        if (!$res["status"]) {
            return response()->json($res);
        }

        $detailAdd = [];
        $detailEdit = [];
        $iAdd = 0;
        $iEdit = 0;
        foreach ($msk_id as $k => $v) {
            if (array_key_exists($k, $asd_id)) {
                $detailEdit[$iEdit] = [
                    "msk_id" => $v,
                    "asd_value" => $asd_value[$k],
                    "as_id" => $as_id,
                    "asd_id" => $asd_id[$k],
                ];

                $dt_detail = $this->asesmenService->getDetailById($asd_id[$k]);

                if (count($dt_detail) > 0) {
                    $detailEdit[$iEdit]["asd_file"] = $dt_detail["asd_file"];
                } else {
                    $detailEdit[$iEdit]["asd_file"] = null;
                }


                if (array_key_exists($k, $lampiran)) {
                    if (!empty($dt_detail["asd_file"])) {
                        $this->asesmenService->hapusFile("./" . $this->dirUploads . "/" . $dt_detail["asd_file"]);
                    }
                    $detailEdit[$iEdit]["asd_file"] = $lampiran[$k];
                }

                $iEdit++;
            } else {
                $detailAdd[$iAdd] = [
                    "msk_id" => $v,
                    "asd_value" => $asd_value[$k],
                    "as_id" => $as_id,
                ];
                $detailAdd[$iAdd]["asd_file"] = null;
                if (array_key_exists($k, $lampiran)) $detailAdd[$iAdd]["asd_file"] = $lampiran[$k];
                $iAdd++;
            }
        }

        if (count($detailAdd) > 0) {
            $res = $this->asesmenService->addDetail($detailAdd);
            if (!$res["status"]) {
                return response()->json($res);
            }
        }

        if (count($detailEdit) > 0) {
            $res = $this->asesmenService->editDetail($detailEdit);
        }

        return response()->json($res);
    }

    public function save(Request $request)
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        DB::beginTransaction();

        $msk_id = $request->msk_id;
        $asd_value = $request->asd_value;
        $asd_id = $request->asd_id;
        $list_hapus_lampiran = $request->list_hapus_lampiran;

        if (empty($msk_id)) $msk_id = [];
        if (empty($asd_value)) $asd_value = [];
        if (empty($asd_id)) $asd_id = [];
        if (empty($list_hapus_lampiran)) $list_hapus_lampiran = "";
        $list_hapus_lampiran = explode(",", $list_hapus_lampiran);
        if (count($list_hapus_lampiran) > 0) {
            $this->asesmenService->hapusLampiran($this->dirUploads, $list_hapus_lampiran);
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
        $cekTenant = $this->asesmenService->getTenantByUser($this->__sess_user["user_id"]);
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

        $hasAsesmen = $this->asesmenService->cekAsesmen($tenant_id);

        if (!$hasAsesmen["status"]) {
            return response()->json($hasAsesmen);
        }

        $as_id = 0;
        if ($hasAsesmen["data"] > 0) {
            $as_id = $hasAsesmen["data"];
            $res = $this->asesmenService->edit($as_id, $data);
        } else {
            $res = $this->asesmenService->add($data);
            if ($res["status"]) $as_id = $res["data"];
        }

        if (!$res["status"]) {
            return response()->json($res);
        }

        $detailAdd = [];
        $detailEdit = [];
        $iAdd = 0;
        $iEdit = 0;
        foreach ($msk_id as $k => $v) {
            if (array_key_exists($k, $asd_id)) {
                $detailEdit[$iEdit] = [
                    "msk_id" => $v,
                    "asd_value" => $asd_value[$k],
                    "as_id" => $as_id,
                    "asd_id" => $asd_id[$k],
                ];

                $dt_detail = $this->asesmenService->getDetailById($asd_id[$k]);

                if (count($dt_detail) > 0) {
                    $detailEdit[$iEdit]["asd_file"] = $dt_detail["asd_file"];
                } else {
                    $detailEdit[$iEdit]["asd_file"] = null;
                }

                if (array_key_exists($k, $lampiran)) {
                    if (!empty($dt_detail["asd_file"])) {
                        $this->asesmenService->hapusFile("./" . $this->dirUploads . "/" . $dt_detail["asd_file"]);
                    }
                    $detailEdit[$iEdit]["asd_file"] = $lampiran[$k];
                }

                $iEdit++;
            } else {
                $detailAdd[$iAdd] = [
                    "msk_id" => $v,
                    "asd_value" => $asd_value[$k],
                    "as_id" => $as_id,
                ];
                $detailAdd[$iAdd]["asd_file"] = null;
                if (array_key_exists($k, $lampiran)) $detailAdd[$iAdd]["asd_file"] = $lampiran[$k];
                $iAdd++;
            }
        }

        if (count($detailAdd) > 0) {
            $res = $this->asesmenService->addDetail($detailAdd);
            if (!$res["status"]) {
                return response()->json($res);
            }
        }

        if (count($detailEdit) > 0) {
            $res = $this->asesmenService->editDetail($detailEdit);
            if (!$res["status"]) {
                return response()->json($res);
            }
        }

        // cek apakah form penilaian sudah diisi semua
        $cekHasComplete = $this->asesmenService->cekHasComplete($tenant_id);
        if (!$cekHasComplete["status"]) {
            return response()->json($cekHasComplete);
        }

        if (!$cekHasComplete["data"]) {
            return response()->json([
                "status" => false,
                "msg" => "Form masih belum lengkap diisi!",
            ]);
        }

        $data = [
            "as_status" => 1,
        ];

        $res = $this->asesmenService->edit($as_id, $data);
        if (!$res["status"]) {
            return response()->json($res);
        }

        // update semua data yang non submission menjadi valid
        $res = $this->asesmenService->editNonSubmission($as_id);

        if (!$res["status"]) {
            return response()->json($res);
        }

        DB::commit();
        return response()->json($res);
    }
}
