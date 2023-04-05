<?php

namespace App\Http\Controllers;

use App\Services\MsSubKriteriaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MsSubKriteriaC extends

MyC
{
    private MsSubKriteriaService $msSubKriteriaService;
    public function __construct(MsSubKriteriaService $msSubKriteriaService)
    {
        parent::__construct();
        $this->middleware("has_akses:ms-sub-kriteria");
        $this->msSubKriteriaService = $msSubKriteriaService;
    }

    public function index(): View
    {
        $optKriteria = [];
        $cekKriteria = $this->msSubKriteriaService->getKriteria();
        if ($cekKriteria["status"]) {
            $optKriteria = $cekKriteria["data"];
        }

        $data = [
            "__title" => "Master Sub Kriteria",
            "optKriteria" => $optKriteria,
        ];

        return $this->my_view("v_ms_sub_kriteria", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "msk.msk_id",
            "msk.msk_kode",
            "msk.msk_nama",
            "msk.msk_bobot",
            "msk.msk_is_submission",
            "msk.msk_status",
            "msk.mk_id",
        ];

        $colsSearch = [
            "msk.msk_kode",
            "msk.msk_nama",
        ];

        $inputSearch = $request->search;
        $inputOrder = $request->order;
        $inputStart = $request->start;
        $inputLength = $request->length;
        $fil_mk_id = $request->fil_mk_id;

        $sWhere = "";
        if (!empty($fil_mk_id)) {
            $sWhere .= " AND msk.mk_id = $fil_mk_id ";
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
        $getTotal = $this->msSubKriteriaService->getTotal($sWhere);
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

        if (!empty($inputLength) && !empty($inputStart)) {
            $sLimit = " LIMIT $inputLength OFFSET $inputStart ";
        }

        $detailData = [];

        $getData = $this->msSubKriteriaService->getData($sWhere, $sOrder, $sLimit, $cols);
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
        $isSubmission = "";
        $aksi = "";

        foreach ($detailData as $v) {

            if ($v->msk_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            if ($v->msk_is_submission == 1) {
                $isSubmission = "<span class='badge badge-success'>Ya</span>";
            } else {
                $isSubmission = "<span class='badge badge-danger'>Tidak</span>";
            }

            $id = $v->msk_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->msk_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";
            $aksi .= $aksiEdit . $aksiHapus;

            $data['data'][] = [
                $no,
                $v->msk_kode,
                $v->msk_nama,
                $v->msk_bobot,
                $isSubmission,
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

        $cekValidasi = $this->msSubKriteriaService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $data = [
            'msk_nama' => $request->msk_nama,
            'msk_kode' => $request->msk_kode,
            'msk_status' => $request->msk_status,
            'msk_bobot' => $request->msk_bobot,
            'msk_is_submission' => $request->msk_is_submission,
            'mk_id' => $request->mk_id,
        ];

        if ($request->act == 'edit') {
            $res = $this->msSubKriteriaService->edit($request->msk_id, $data);
        } else {
            $res = $this->msSubKriteriaService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->msSubKriteriaService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->msSubKriteriaService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->msSubKriteriaService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "msk_id" => $dt->msk_id,
                "msk_nama" => $dt->msk_nama,
                "msk_kode" => $dt->msk_kode,
                "msk_bobot" => $dt->msk_bobot,
                "msk_status" => $dt->msk_status,
                "msk_is_submission" => $dt->msk_is_submission,
                "mk_id" => $dt->mk_id,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }
}
