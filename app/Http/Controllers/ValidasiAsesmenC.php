<?php

namespace App\Http\Controllers;

use App\Services\ValidasiAsesmenService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ValidasiAsesmenC extends MyC
{
    private ValidasiAsesmenService $validasiAsesmenService;
    private $dirUploads = "uploads/lampiran_submission";
    public function __construct(ValidasiAsesmenService $validasiAsesmenService)
    {
        parent::__construct();
        parent::middleware("has_akses:validasi-asesmen");
        $this->validasiAsesmenService = $validasiAsesmenService;
    }

    public function index(): View
    {
        $optKu = [];
        $dtKu = $this->validasiAsesmenService->getKategoriUsaha();
        if ($dtKu["status"]) {
            $optKu = $dtKu["data"];
        }
        $dtKriteria = [];
        $cekKriteria = $this->validasiAsesmenService->getKriteria();
        if ($cekKriteria["status"]) {
            $dtKriteria = $cekKriteria["data"];
        }

        $data = [
            "__title" => "Validasi Asesmen",
            "opt_ku" => $optKu,
            "dtKriteria" => $dtKriteria,
            "dirUploads" => $this->dirUploads,
        ];

        return $this->my_view("v_validasi_asesmen", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "a.as_id",
            "a.created_at",
            "t.tenant_nama",
        ];

        $colsSearch = [
            "t.tenant_nama",
        ];

        $inputSearch = $request->search;
        $inputOrder = $request->order;
        $inputStart = $request->start;
        $inputLength = $request->length;
        $fil_mku_id = $request->fil_mku_id;
        $fil_as_status = $request->fil_as_status;

        $sWhere = "";
        $sWhere .= " AND a.as_status = $fil_as_status AND t.tenant_status = 1 ";

        if (!empty($fil_mku_id)) {
            $sWhere .= " AND t.mku_id = $fil_mku_id ";
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
        $getTotal = $this->validasiAsesmenService->getTotal($sWhere);
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

        $getData = $this->validasiAsesmenService->getData($sWhere, $sOrder, $sLimit, $cols);
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

            $id = $v->as_id;

            if ($fil_as_status == 0) {
                $aksiCek = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Validasi" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-check"></i></a>';
            } else {
                $aksiCek = '<a href="javascript:void(0)" class="btn btn-sm btn-info mb-1 mx-1" title="Preview" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-eye"></i></a>';
            }

            $aksi = "";
            $aksi .= $aksiCek;

            $data['data'][] = [
                $no,
                $v->created_at,
                $v->tenant_nama,
                $aksi,
            ];

            $no++;
        }

        return response()->json($data);
    }

    public function edit(string $id): JsonResponse
    {
        $res = $this->validasiAsesmenService->edit($id);
        return response()->json($res);
    }

    public function validasi(Request $request): JsonResponse
    {
        $id = $request->id;
        $val = $request->val;

        $data = [
            "asd_status" => $val,
            "user_id" => $this->__sess_user["user_id"],
        ];

        $res = $this->validasiAsesmenService->updateDetail($id, $data);
        return response()->json($res);
    }

    public function final(string $id): JsonResponse
    {
        $res = $this->validasiAsesmenService->update($id, [
            "as_status" => 2,
            "valid_by" => $this->__sess_user["user_id"],
        ]);
        return response()->json($res);
    }
}
