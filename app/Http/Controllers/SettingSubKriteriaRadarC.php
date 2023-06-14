<?php

namespace App\Http\Controllers;

use App\Services\SettingSubKriteriaRadarService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingSubKriteriaRadarC extends MyC
{
    private SettingSubKriteriaRadarService $settingSubKriteriaRadarService;
    public function __construct(SettingSubKriteriaRadarService $settingSubKriteriaRadarService)
    {
        parent::__construct();
        $this->middleware("has_akses:setting-sub-kriteria-radar");
        $this->settingSubKriteriaRadarService = $settingSubKriteriaRadarService;
    }

    public function index(): View
    {
        $optDimensi = [];
        $cekDimensi = $this->settingSubKriteriaRadarService->getDimensi();
        if ($cekDimensi['status']) {
            $optDimensi = $cekDimensi['data'];
        }

        $data = [
            "__title" => "Setting Sub Kriteria - Radar",
            "dimensi" => $optDimensi,
        ];

        return $this->my_view("v_setting_sub_kriteria_radar", $data);
    }

    public function getData(string $jenis, Request $request): JsonResponse
    {
        $cols = [
            "mr.mr_id",
            "mr.mr_id",
            "mr.mr_kode",
            "mr.mr_nama",
        ];

        $msk_id         = $request->msk_id;
        $inputSearch    = $request->search;
        $inputOrder     = $request->order;
        $inputStart     = $request->start;
        $inputLength    = $request->length;
        if (empty($msk_id)) {
            $msk_id = 0;
        }

        $subWhere = " AND mr.mr_id " . ($jenis == "before" ? "not" : "") . " in (
        select
            sskr.mr_id
        from
            setting_sub_kriteria_radar sskr
        where
            sskr.msk_id = $msk_id ) ";

        $sWhere = "";
        $sWhere .= $subWhere;

        if (!empty($inputSearch) && array_key_exists("value", $inputSearch)) {
            if (!empty($inputSearch['value'])) {
                $search = $inputSearch['value'];
                $sWhere .= " AND (";
                for ($i = 0; $i < count($cols); $i++) {
                    $sWhere .= " lower(cast(" . $cols[$i] . " as char)) like lower('%$search%') or ";
                }
                $sWhere = substr($sWhere, 0, -3);

                $sWhere .= ")";
            }
        }

        $totalData = 0;
        $getTotal = $this->settingSubKriteriaRadarService->getTotal($sWhere);
        if ($getTotal['status']) {
            $totalData = $getTotal['total'];
        }

        $sOrder = " order by ";

        if (!empty($inputOrder)) {
            if (count($inputOrder) > 0) {
                foreach ($inputOrder as $v) {
                    $sOrder .= " " . $cols[$v["column"]] . " " . $v["dir"] . ',';
                }

                $sOrder = substr($sOrder, 0, -1);
            } else {
                $sOrder .= $cols[0] . " asc ";
            }
        } else {
            $sOrder .= $cols[0] . " asc ";
        }

        $sLimit = "";

        if ((!empty($inputLength) || $inputLength == 0) && (!empty($inputStart) || $inputStart == 0)) {
            $sLimit = " LIMIT $inputLength OFFSET $inputStart ";
        }

        $detailData = [];

        $getData = $this->settingSubKriteriaRadarService->getData($sWhere, $sOrder, $sLimit, $cols);
        if ($getData['status']) {
            $detailData = $getData['data'];
        }

        $data = [
            'recordsFiltered' => $totalData,
            'recordsTotal' => $totalData,
            'data' => [],
        ];

        $i = 1;
        $no = $i + $inputStart;

        foreach ($detailData as $v) {
            $id = $v->mr_id;

            $inputCheck = '<input type="checkbox" value="' . $id . '">';

            $data['data'][] = [
                $no,
                $inputCheck,
                $v->mr_kode,
                $v->mr_nama,
            ];

            $no++;
        }

        return response()->json($data);
    }

    public function getParent(Request $request): JsonResponse
    {
        $res = [
            "status" => true,
            "message" => "",
        ];

        $jenis = $request->jenis;
        $parent = $request->parent;
        if (empty($jenis)) {
            return response()->json([
                "status" => false,
                "message" => "Parameter jenis tidak diketahui",
            ]);
        }

        if (empty($parent)) {
            $parent = 0;
        }

        if ($jenis == "kriteria") {
            $res = $this->settingSubKriteriaRadarService->getKriteria($parent);
        } else {
            $res = $this->settingSubKriteriaRadarService->getSubKriteria($parent);
        }

        return response()->json($res);
    }

    public function save(Request $request): JsonResponse
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        $jenis = $request->jenis;
        $msk_id = $request->msk_id;
        $mr_id = $request->mr_id;

        if (empty($jenis) || empty($msk_id) || empty($mr_id)) {
            return response()->json([
                "status" => false,
                "message" => "Data tidak lengkap",
            ]);
        }

        if ($jenis == "kiri") {
            $res = $this->settingSubKriteriaRadarService->del($msk_id, $mr_id);
        } else {
            $data = [];
            foreach ($mr_id as $v) {
                $data[] = [
                    'mr_id' => $v,
                    'msk_id' => $msk_id,
                ];
            }
            $res = $this->settingSubKriteriaRadarService->add($data);
        }

        return response()->json($res);
    }
}
