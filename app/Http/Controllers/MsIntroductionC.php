<?php

namespace App\Http\Controllers;

use App\Services\MsIntroductionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MsIntroductionC extends MyC
{
    private MsIntroductionService $msIntroductionService;
    public function __construct(MsIntroductionService $msIntroductionService)
    {
        parent::__construct();
        $this->middleware("has_akses:ms-pengenalan");
        $this->msIntroductionService = $msIntroductionService;
    }

    public function index(): View
    {
        $data = [
            "__title" => "Master Pengenalan",
        ];

        return $this->my_view("v_ms_introduction", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "mi.mi_id",
            "mi.mi_kode",
            "mi.mi_nama",
            "mi.mi_status",
            "mi.mi_isi",
        ];

        $colsSearch = [
            "mi.mi_kode",
            "mi.mi_nama",
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
        $getTotal = $this->msIntroductionService->getTotal($sWhere);
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

        $getData = $this->msIntroductionService->getData($sWhere, $sOrder, $sLimit, $cols);
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

            if ($v->mi_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->mi_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->mi_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";
            $aksi .= $aksiEdit . $aksiHapus;

            $data['data'][] = [
                $no,
                $v->mi_kode,
                $v->mi_nama,
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

        $cekValidasi = $this->msIntroductionService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $data = [
            'mi_nama' => $request->mi_nama,
            'mi_kode' => $request->mi_kode,
            'mi_isi' => rawurlencode($request->mi_isi),
            'mi_status' => $request->mi_status,
        ];

        if ($request->act == 'edit') {
            $res = $this->msIntroductionService->edit($request->mi_id, $data);
        } else {
            $res = $this->msIntroductionService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->msIntroductionService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->msIntroductionService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->msIntroductionService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "mi_id" => $dt->mi_id,
                "mi_nama" => $dt->mi_nama,
                "mi_kode" => $dt->mi_kode,
                "mi_isi" => $dt->mi_isi,
                "mi_status" => $dt->mi_status,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }
}
