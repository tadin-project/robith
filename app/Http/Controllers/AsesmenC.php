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
        $dtIntroduction = [];
        $dtKriteria = [];
        $dtSubKriteria = [];
        $dtConvertionValue = [];

        $cekIntroduction = $this->asesmenService->getIntroduction();
        if ($cekIntroduction["status"]) {
            $dtIntroduction = $cekIntroduction["data"];
        }

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
            "dtIntroduction" => $dtIntroduction,
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

        $sskr_id = $request->sskr_id;
        $asd_value = $request->asd_value;
        $asd_id = $request->asd_id;

        $msk_id = $request->msk_id;
        $asf_id = $request->asf_id;
        $list_hapus_lampiran = $request->list_hapus_lampiran;

        if (empty($sskr_id)) $sskr_id = [];
        if (empty($asd_value)) $asd_value = [];
        if (empty($asd_id)) $asd_id = [];

        if (empty($asf_id)) $asf_id = [];
        if (empty($msk_id)) $msk_id = [];
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

        if (count($sskr_id) <= 0) {
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
        $detailAddFile = [];
        $detailEditFile = [];
        $iAdd = 0;
        $iEdit = 0;
        $iAddFile = 0;
        $iEditFile = 0;
        foreach ($sskr_id as $k => $v) {
            if (array_key_exists($k, $asd_id)) {
                $detailEdit[$iEdit] = [
                    "asd_value" => $asd_value[$k],
                    "asd_id" => $asd_id[$k],
                ];

                $iEdit++;
            } else {
                $detailAdd[$iAdd] = [
                    "sskr_id" => $v,
                    "asd_value" => $asd_value[$k],
                    "as_id" => $as_id,
                ];
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

        foreach ($msk_id as $k => $v) {
            if (array_key_exists($k, $asf_id)) {
                if (array_key_exists($k, $lampiran)) {
                    $dt_detail = $this->asesmenService->getFileById($asf_id[$k]);

                    if (!empty($dt_detail["asf_file"])) {
                        $this->asesmenService->hapusFile("./" . $this->dirUploads . "/" . $dt_detail["asf_file"]);
                    }

                    $detailEditFile[$iEditFile] = [
                        "asf_id" => $asf_id[$k], "asf_file" => $lampiran[$k],
                    ];

                    $iEditFile++;
                }
            } else {
                $detailAddFile[$iAddFile] = [
                    "msk_id" => $v,
                    "as_id" => $as_id,
                    "asf_file" => null,
                ];

                if (array_key_exists($k, $lampiran)) $detailAddFile[$iAddFile]["asf_file"] = $lampiran[$k];
                $iAddFile++;
            }
        }

        if (count($detailAddFile) > 0) {
            $res = $this->asesmenService->addFile($detailAddFile);
            if (!$res["status"]) {
                return response()->json($res);
            }
        }

        if (count($detailEditFile) > 0) {
            $res = $this->asesmenService->editFile($detailEditFile);
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
        $sskr_id = $request->sskr_id;
        $asd_value = $request->asd_value;
        $asd_id = $request->asd_id;

        $msk_id = $request->msk_id;
        $asf_id = $request->asf_id;
        $list_hapus_lampiran = $request->list_hapus_lampiran;

        if (empty($sskr_id)) $sskr_id = [];
        if (empty($asd_value)) $asd_value = [];
        if (empty($asd_id)) $asd_id = [];

        if (empty($asf_id)) $asf_id = [];
        if (empty($msk_id)) $msk_id = [];
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

        if (count($sskr_id) <= 0) {
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
        $detailAddFile = [];
        $detailEditFile = [];
        $iAdd = 0;
        $iEdit = 0;
        $iAddFile = 0;
        $iEditFile = 0;

        foreach ($sskr_id as $k => $v) {
            if (array_key_exists($k, $asd_id)) {
                $detailEdit[$iEdit] = [
                    "asd_value" => $asd_value[$k],
                    "asd_id" => $asd_id[$k],
                ];

                $iEdit++;
            } else {
                $detailAdd[$iAdd] = [
                    "sskr_id" => $v,
                    "asd_value" => $asd_value[$k],
                    "as_id" => $as_id,
                ];
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

        foreach ($msk_id as $k => $v) {
            if (array_key_exists($k, $asf_id)) {
                if (array_key_exists($k, $lampiran)) {
                    $dt_detail = $this->asesmenService->getFileById($asf_id[$k]);

                    if (!empty($dt_detail["asf_file"])) {
                        $this->asesmenService->hapusFile("./" . $this->dirUploads . "/" . $dt_detail["asf_file"]);
                    }

                    $detailEditFile[$iEditFile] = [
                        "asf_id" => $asf_id[$k], "asf_file" => $lampiran[$k],
                    ];
                    $iEditFile++;
                }
            } else {
                $detailAddFile[$iAddFile] = [
                    "msk_id" => $v,
                    "as_id" => $as_id,
                    "asf_file" => null,
                ];

                if (array_key_exists($k, $lampiran)) $detailAddFile[$iAddFile]["asf_file"] = $lampiran[$k];
                $iAddFile++;
            }
        }

        if (count($detailAddFile) > 0) {
            $res = $this->asesmenService->addFile($detailAddFile);
            if (!$res["status"]) {
                return response()->json($res);
            }
        }

        if (count($detailEditFile) > 0) {
            $res = $this->asesmenService->editFile($detailEditFile);
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
        /*
        $res = $this->asesmenService->editNonSubmission($as_id);

        if (!$res["status"]) {
            return response()->json($res);
        }
        */

        DB::commit();
        return response()->json($res);
    }
}
