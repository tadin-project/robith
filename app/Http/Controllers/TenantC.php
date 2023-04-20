<?php

namespace App\Http\Controllers;

use App\Services\TenantService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantC extends MyC
{
    private TenantService $tenantService;
    public function __construct(TenantService $tenantService)
    {
        parent::__construct();
        $this->middleware("has_akses:tenant");
        $this->tenantService = $tenantService;
    }

    public function index(): View
    {
        $optKu = [];
        $dtKu = $this->tenantService->getKategoriUsaha();
        if ($dtKu["status"]) {
            $optKu = $dtKu["data"];
        }

        $data = [
            "__title" => "Data Tenant",
            "opt_ku" => $optKu,
        ];

        return $this->my_view("v_tenant", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "t.tenant_id",
            "t.tenant_nama",
            "mu.user_name",
            "mku.mku_nama",
            "t.tenant_status",
            "mu.user_fullname",
        ];

        $colsSearch = [
            "t.tenant_nama",
            "mu.user_name",
            "mku.mku_nama",
            "mu.user_fullname",
        ];

        $inputSearch = $request->search;
        $inputOrder = $request->order;
        $inputStart = $request->start;
        $inputLength = $request->length;
        $fil_mku_id = $request->fil_mku_id;

        $sWhere = "";

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
        $getTotal = $this->tenantService->getTotal($sWhere);
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

        $getData = $this->tenantService->getData($sWhere, $sOrder, $sLimit, $cols);
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

            if ($v->tenant_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->tenant_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->tenant_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";
            $aksi .= $aksiEdit . $aksiHapus;

            $data['data'][] = [
                $no,
                $v->tenant_nama,
                $v->user_fullname . " (" . $v->user_name . ")",
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

        $cekValidasi = $this->tenantService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $data = [
            'tenant_nama' => $request->tenant_nama,
            'tenant_desc' => $request->tenant_desc,
            'tenant_status' => $request->tenant_status,
            'mku_id' => $request->mku_id,
            'user_id' => $request->user_id,
        ];

        if ($request->act == 'edit') {
            $res = $this->tenantService->edit($request->tenant_id, $data);
        } else {
            $res = $this->tenantService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->tenantService->del($id);

        return response()->json($res);
    }

    public function getById($id): JsonResponse
    {
        $res = $this->tenantService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "tenant_id" => $dt->tenant_id,
                "tenant_nama" => $dt->tenant_nama,
                "tenant_desc" => $dt->tenant_desc,
                "tenant_status" => $dt->tenant_status,
                "mku_id" => $dt->mku_id,
                "user_id" => $dt->user_id,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }

    public function getUsers(Request $request): JsonResponse
    {
        $act = $request->act;
        $oldUser = $request->old_user;
        if (empty($act)) $act = "";
        if (empty($oldUser)) $oldUser = "";

        $res = $this->tenantService->getUsers($act, $oldUser);
        return response()->json($res);
    }
}
