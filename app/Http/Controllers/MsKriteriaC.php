<?php

namespace App\Http\Controllers;

use App\Services\MsKriteriaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MsKriteriaC extends MyC
{
    private MsKriteriaService $msKriteriaService;
    public function __construct(MsKriteriaService $msKriteriaService)
    {
        parent::__construct();
        $this->middleware("has_akses:ms-kriteria");
        $this->msKriteriaService = $msKriteriaService;
    }

    public function index(): View
    {
        $optDimensi = [];
        $cekDimensi = $this->msKriteriaService->getDimensi();
        if ($cekDimensi["status"]) {
            $optDimensi = $cekDimensi["data"];
        }

        $data = [
            "__title" => "Master Kriteria",
            "optDimensi" => $optDimensi,
        ];

        return $this->my_view("v_ms_kriteria", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "mk.mk_id",
            "mk.mk_kode",
            "mk.mk_nama",
            "coalesce(msk.tot_bobot, 0) as tot_bobot",
            "mk.mk_status",
        ];

        $colsSearch = [
            "mk.mk_kode",
            "mk.mk_nama",
        ];

        $inputSearch = $request->search;
        $inputOrder = $request->order;
        $inputStart = $request->start;
        $inputLength = $request->length;
        $fil_md_id = $request->fil_md_id;

        $sWhere = "";
        if (!empty($fil_md_id)) {
            $sWhere .= " AND mk.md_id = $fil_md_id ";
        }

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
        $getTotal = $this->msKriteriaService->getTotal($sWhere);
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

        $getData = $this->msKriteriaService->getData($sWhere, $sOrder, $sLimit, $cols);
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

            if ($v->mk_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->mk_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->mk_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";
            $aksi .= $aksiEdit . $aksiHapus;

            $data['data'][] = [
                $no,
                $v->mk_kode,
                $v->mk_nama,
                $v->tot_bobot,
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

        $cekValidasi = $this->msKriteriaService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $data = [
            'mk_nama' => $request->mk_nama,
            'mk_kode' => $request->mk_kode,
            'mk_status' => $request->mk_status,
            'md_id' => $request->md_id,
        ];

        if ($request->act == 'edit') {
            $res = $this->msKriteriaService->edit($request->mk_id, $data);
        } else {
            $res = $this->msKriteriaService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->msKriteriaService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->msKriteriaService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->msKriteriaService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "mk_id" => $dt->mk_id,
                "mk_nama" => $dt->mk_nama,
                "mk_kode" => $dt->mk_kode,
                "mk_status" => $dt->mk_status,
                "md_id" => $dt->md_id,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }
}
