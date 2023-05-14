<?php

namespace App\Http\Controllers;

use App\Services\ConvertionValueService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConvertionValueC extends MyC
{
    private ConvertionValueService $convertionValueService;
    public function __construct(ConvertionValueService $convertionValueService)
    {
        parent::__construct();
        $this->middleware("has_akses:convertion-value");
        $this->convertionValueService = $convertionValueService;
    }

    public function index(): View
    {
        $data = [
            "__title" => "Master Presentase Penilaian",
        ];

        return $this->my_view("v_convertion_value", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "cv.cval_id",
            "cv.cval_kode",
            "cv.cval_nama",
            "cv.cval_nilai",
            "cv.cval_status",
        ];

        $colsSearch = [
            "cv.cval_kode",
            "cv.cval_nama",
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
        $getTotal = $this->convertionValueService->getTotal($sWhere);
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

        $getData = $this->convertionValueService->getData($sWhere, $sOrder, $sLimit, $cols);
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

            if ($v->cval_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->cval_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->cval_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";
            $aksi .= $aksiEdit . $aksiHapus;

            $data['data'][] = [
                $no,
                $v->cval_kode,
                $v->cval_nama,
                $v->cval_nilai,
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

        $cekValidasi = $this->convertionValueService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $cval_nilai = $request->cval_nilai;
        if (empty($cval_nilai)) $cval_nilai = 0;

        $data = [
            'cval_nama' => $request->cval_nama,
            'cval_kode' => $request->cval_kode,
            'cval_nilai' => $cval_nilai,
            'cval_status' => $request->cval_status,
        ];

        if ($request->act == 'edit') {
            $res = $this->convertionValueService->edit($request->cval_id, $data);
        } else {
            $res = $this->convertionValueService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->convertionValueService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->convertionValueService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->convertionValueService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "cval_id" => $dt->cval_id,
                "cval_nama" => $dt->cval_nama,
                "cval_kode" => $dt->cval_kode,
                "cval_nilai" => $dt->cval_nilai,
                "cval_status" => $dt->cval_status,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }
}
