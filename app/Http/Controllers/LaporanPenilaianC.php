<?php

namespace App\Http\Controllers;

use App\Services\LaporanPenilaianService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanPenilaianC extends MyC
{
    private LaporanPenilaianService $LaporanPenilaianService;
    public function __construct(LaporanPenilaianService $LaporanPenilaianService)
    {
        parent::__construct();
        $this->middleware("has_akses:laporan-penilaian");
        $this->LaporanPenilaianService = $LaporanPenilaianService;
    }

    public function index(): View
    {
        $data = [
            "__title" => "Laporan Penilaian",
        ];

        return $this->my_view("v_laporan_penilaian", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "a.as_id",
            "a.updated_at",
            "coalesce(mu.user_fullname, mu.user_name) as user_tenant",
            "coalesce(v.user_fullname, v.user_name) as validator",
            "a.as_total",
            "a.as_max",
            "t.tenant_nama",
        ];

        $colsSearch = [
            "a.updated_at",
            "v.user_fullname",
            "v.user_name",
            "mu.user_fullname",
            "mu.user_name",
            "t.tenant_nama",
            "a.as_total",
            "a.as_max",
        ];

        $colsOrder = [
            "a.as_id",
            "a.updated_at",
            "mu.user_fullname",
            "v.user_fullname",
            "a.as_total",
        ];

        $inputSearch = $request->search;
        $inputOrder = $request->order;
        $inputStart = $request->start;
        $inputLength = $request->length;

        $sWhere = "";

        if (!empty($inputSearch) && array_key_exists("value", $inputSearch)) {
            if (!empty($inputSearch['value'])) {
                $search = $inputSearch['value'];
                $sWhere .= " AND (";
                for ($i = 0; $i < count($colsSearch); $i++) {
                    $sWhere .= " lower(cast(" . $colsSearch[$i] . " as char)) like lower('%$search%') or ";
                }
                $sWhere = substr($sWhere, 0, -3);

                $sWhere .= ")";
            }
        }

        $totalData = 0;
        $getTotal = $this->LaporanPenilaianService->getTotal($sWhere);
        if ($getTotal['status']) {
            $totalData = $getTotal['total'];
        }

        $sOrder = " order by ";

        if (!empty($inputOrder)) {
            if (count($inputOrder) > 0) {
                foreach ($inputOrder as $v) {
                    $sOrder .= " " . $colsOrder[$v["column"]] . " " . $v["dir"] . ',';
                }

                $sOrder = substr($sOrder, 0, -1);
            } else {
                $sOrder .= $colsOrder[0] . " asc ";
            }
        } else {
            $sOrder .= $colsOrder[0] . " asc ";
        }

        $sLimit = "";

        if ((!empty($inputLength) || $inputLength == 0) && (!empty($inputStart) || $inputStart == 0)) {
            $sLimit = " LIMIT $inputLength OFFSET $inputStart ";
        }

        $detailData = [];

        $getData = $this->LaporanPenilaianService->getData($sWhere, $sOrder, $sLimit, $cols);
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
        $aksi = "";

        foreach ($detailData as $v) {
            $id = $v->as_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-info mb-1 mx-1" title="Lihat" onclick="fnPreview(\'' . $id . '\')"><i class="fas fa-eye"></i></a>';

            $aksi = "";
            $aksi .= $aksiEdit;

            $data['data'][] = [
                $no,
                $v->updated_at,
                $v->user_tenant . " (" . $v->tenant_nama . ")",
                $v->validator,
                ($v->as_total * 100 / $v->as_max) . "% (" . $v->as_total . " / " . $v->as_max . ")",
                $aksi,
            ];

            $no++;
        }

        return response()->json($data);
    }

    public function getById($id): JsonResponse
    {
        $res = $this->LaporanPenilaianService->getById($id);
        return response()->json($res);
    }
}
