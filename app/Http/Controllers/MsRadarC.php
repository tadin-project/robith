<?php

namespace App\Http\Controllers;

use App\Services\MsRadarService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MsRadarC extends MyC
{
    private MsRadarService $msRadarService;
    public function __construct(MsRadarService $msRadarService)
    {
        parent::__construct();
        $this->middleware("has_akses:ms-radar");
        $this->msRadarService = $msRadarService;
    }

    public function index(): View
    {
        $data = [
            "__title" => "Master Radar",
        ];

        return $this->my_view("v_ms_radar", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "mr.mr_id",
            "mr.mr_kode",
            "mr.mr_nama",
            "mr.mr_color",
            "mr.mr_status",
            "mr.mr_bobot",
        ];

        $colsSearch = [
            "mr.mr_kode",
            "mr.mr_nama",
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
        $getTotal = $this->msRadarService->getTotal($sWhere);
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

        $getData = $this->msRadarService->getData($sWhere, $sOrder, $sLimit, $cols);
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

            if ($v->mr_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->mr_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->mr_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";
            $aksi .= $aksiEdit . $aksiHapus;

            $data['data'][] = [
                $no,
                $v->mr_kode,
                $v->mr_nama,
                $v->mr_color . '&nbsp;<i class="fas fa-square" style="color: ' . $v->mr_color . ';"></i>',
                $v->mr_bobot,
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

        $cekValidasi = $this->msRadarService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $mr_bobot = $request->mr_bobot;
        if (empty($mr_bobot)) {
            $mr_bobot = 0;
        }

        $data = [
            'mr_nama' => $request->mr_nama,
            'mr_kode' => $request->mr_kode,
            'mr_color' => $request->mr_color,
            'mr_status' => $request->mr_status,
            'mr_bobot' => $mr_bobot,
        ];

        if ($request->act == 'edit') {
            $res = $this->msRadarService->edit($request->mr_id, $data);
        } else {
            $res = $this->msRadarService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->msRadarService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->msRadarService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->msRadarService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "mr_id" => $dt->mr_id,
                "mr_nama" => $dt->mr_nama,
                "mr_kode" => $dt->mr_kode,
                "mr_color" => $dt->mr_color,
                "mr_status" => $dt->mr_status,
                "mr_bobot" => $dt->mr_bobot,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }
}
