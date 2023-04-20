<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MyC;
use App\Services\MsUsersService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MsUsersC extends MyC
{
    private MsUsersService $msUsersService;
    public function __construct(MsUsersService $msUsersService)
    {
        parent::__construct();
        $this->middleware("has_akses:ms-users");
        $this->msUsersService = $msUsersService;
    }

    public function index(): View
    {
        $cekOptGroup = $this->msUsersService->getOptGroup(($this->__sess_user["group_id"] == 1 ? true : false));
        $optGroup = [];

        if ($cekOptGroup['status']) {
            $optGroup = $cekOptGroup["data"];
        }

        $data = [
            "__title" => "Master User",
            "opt_group" => $optGroup,
        ];

        return $this->my_view("v_ms_user", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "mu.user_id",
            "mu.user_name",
            "mu.user_fullname",
            "mu.user_email",
            "mg.group_nama",
            "mu.user_status",
        ];

        $colsSearch = [
            "mu.user_name",
            "mu.user_fullname",
            "mu.user_email",
            "mg.group_nama",
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
        $getTotal = $this->msUsersService->getTotal($sWhere);
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

        $getData = $this->msUsersService->getData($sWhere, $sOrder, $sLimit, $cols);
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

            if ($v->user_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->user_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->user_name . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";

            if ($id == 1) {
                if ($this->__sess_user['user_id'] == 1) {
                    $aksi .= $aksiEdit;
                } else {
                    continue;
                }
            } else if ($id == 2) {
                if ($this->__sess_user['user_id'] == 1 || $this->__sess_user['user_id'] == 2) {
                    $aksi .= $aksiEdit;
                } else {
                    $aksi = '';
                }
            } else if ($id == $this->__sess_user['user_id']) {
                $aksi .= $aksiEdit;
            } else {
                $aksi .= $aksiEdit . $aksiHapus;
            }

            $data['data'][] = [
                $no,
                $v->user_name,
                $v->user_fullname,
                $v->user_email,
                $v->group_nama,
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

        $cekValidasi = $this->msUsersService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $data = [
            'user_name' => $request->user_name,
            'user_fullname' => $request->user_fullname,
            'user_email' => $request->user_email,
            'user_status' => $request->user_status,
            'group_id' => $request->group_id,
        ];

        if ($request->act == 'edit') {
            if ($request->is_ganti_pass) {
                $data["user_password"] = Hash::make($request->user_password);
            }
            $res = $this->msUsersService->edit($request->user_id, $data);
        } else {
            $data["user_password"] = Hash::make($request->user_password);
            $res = $this->msUsersService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->msUsersService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->msUsersService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->msUsersService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "user_id" => $dt->user_id,
                "user_name" => $dt->user_name,
                "user_fullname" => $dt->user_fullname,
                "user_email" => $dt->user_email,
                "user_status" => $dt->user_status,
                "group_id" => $dt->group_id,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }
}
