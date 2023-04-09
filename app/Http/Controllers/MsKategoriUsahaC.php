<?php

namespace App\Http\Controllers;

use App\Services\MsKategoriUsahaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MsKategoriUsahaC extends MyC
{
    private MsKategoriUsahaService $msKategoriUsahaService;
    public function __construct(MsKategoriUsahaService $msKategoriUsahaService)
    {
        parent::__construct();
        $this->middleware("has_akses:ms-kategori-usaha");
        $this->msKategoriUsahaService = $msKategoriUsahaService;
    }

    public function index(): View
    {
        $data = [
            "__title" => "Master Kategori Usaha",
        ];

        return $this->my_view("v_ms_kategori_usaha", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "mku.mku_id",
            "mku.mku_kode",
            "mku.mku_nama",
            "mku.mku_status",
        ];

        $colsSearch = [
            "mku.mku_kode",
            "mku.mku_nama",
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
        $getTotal = $this->msKategoriUsahaService->getTotal($sWhere);
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

        $getData = $this->msKategoriUsahaService->getData($sWhere, $sOrder, $sLimit, $cols);
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

            if ($v->mku_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->mku_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->mku_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";
            $aksi .= $aksiEdit . $aksiHapus;

            $data['data'][] = [
                $no,
                $v->mku_kode,
                $v->mku_nama,
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

        $cekValidasi = $this->msKategoriUsahaService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $data = [
            'mku_nama' => $request->mku_nama,
            'mku_kode' => $request->mku_kode,
            'mku_status' => $request->mku_status,
        ];

        if ($request->act == 'edit') {
            $res = $this->msKategoriUsahaService->edit($request->mku_id, $data);
        } else {
            $res = $this->msKategoriUsahaService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->msKategoriUsahaService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->msKategoriUsahaService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->msKategoriUsahaService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "mku_id" => $dt->mku_id,
                "mku_nama" => $dt->mku_nama,
                "mku_kode" => $dt->mku_kode,
                "mku_status" => $dt->mku_status,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }
}
