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
        $data = [
            "__title" => "Master Dimensi",
        ];

        return $this->my_view("v_ms_dimensi", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "md.md_id",
            "md.md_kode",
            "md.md_nama",
            "md.md_status",
        ];

        $colsSearch = [
            "md.md_kode",
            "md.md_nama",
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

        $status = "";
        $aksi = "";

        foreach ($detailData as $v) {

            if ($v->md_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->md_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->md_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";
            $aksi .= $aksiEdit . $aksiHapus;

            $data['data'][] = [
                $no,
                $v->md_kode,
                $v->md_nama,
                $status,
                $aksi,
            ];

            $no++;
        }

        return response()->json($data);
    }

    public function save(Request $request): JsonResponse
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        $cekValidasi = $this->settingSubKriteriaRadarService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $data = [
            'md_nama' => $request->md_nama,
            'md_kode' => $request->md_kode,
            'md_status' => $request->md_status,
        ];

        if ($request->act == 'edit') {
            $res = $this->settingSubKriteriaRadarService->edit($request->md_id, $data);
        } else {
            $res = $this->settingSubKriteriaRadarService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->settingSubKriteriaRadarService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->settingSubKriteriaRadarService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->settingSubKriteriaRadarService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "md_id" => $dt->md_id,
                "md_nama" => $dt->md_nama,
                "md_kode" => $dt->md_kode,
                "md_status" => $dt->md_status,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }
}
