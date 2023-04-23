<?php

namespace App\Http\Controllers;

use App\Services\MsMenusService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MsMenusC extends MyC
{
    private MsMenusService $msMenusService;
    public function __construct(MsMenusService $msMenusService)
    {
        parent::__construct();
        $this->middleware("has_akses:ms-menus");
        $this->msMenusService = $msMenusService;
    }

    public function index(): View
    {
        $cekOptParent = $this->msMenusService->getOptParent();
        $optParent = [];

        if ($cekOptParent['status']) {
            $optParent = $cekOptParent["data"];
        }

        $data = [
            "__title" => "Master Menu",
            "opt_group" => $optParent,
        ];

        return $this->my_view("v_ms_menu", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "mm.menu_id",
            "mm.menu_kode",
            "mm.menu_nama",
            "mm.menu_type",
            "mm.menu_link",
            "mm.menu_ikon",
            "p.menu_nama as parent_menu_nama",
            "mm.menu_status",
            "coalesce(c.tot_child) as tot_child",
        ];

        $colsSearch = [
            "mm.menu_id",
            "mm.menu_kode",
            "mm.menu_nama",
            "mm.menu_type",
            "mm.menu_link",
            "mm.menu_ikon",
            "p.menu_nama",
            "mm.menu_status",
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
        $getTotal = $this->msMenusService->getTotal($sWhere);
        if ($getTotal['status']) {
            $totalData = $getTotal['total'];
        }

        $sOrder = " order by ";

        if (!empty($inputOrder)) {
            if (count($inputOrder) > 0) {
                foreach ($inputOrder as $v) {
                    $sOrder .= " " . $colsSearch[$v["column"]] . " " . $v["dir"] . ',';
                }

                $sOrder = substr($sOrder, 0, -1);
            } else {
                $sOrder .= $colsSearch[0] . " asc ";
            }
        } else {
            $sOrder .= $colsSearch[0] . " asc ";
        }

        $sLimit = "";

        if ((!empty($inputLength) || $inputLength == 0) && (!empty($inputStart) || $inputStart == 0)) {
            $sLimit = " LIMIT $inputLength OFFSET $inputStart ";
        }

        $detailData = [];

        $getData = $this->msMenusService->getData($sWhere, $sOrder, $sLimit, $cols);
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

            if ($v->menu_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->menu_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->menu_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";

            if ($id <= 7) {
                if ($this->__sess_user['user_id'] == 1) {
                    $aksi .= $aksiEdit;
                }
            } else {
                $aksi .= $aksiEdit;
                if ($v->tot_child <= 0) {
                    $aksi .= $aksiHapus;
                }
            }

            $data['data'][] = [
                $no,
                $v->menu_kode,
                $v->menu_nama,
                $v->menu_type == 1 ? "Link" : "Title",
                $v->menu_link,
                $v->menu_ikon,
                $v->parent_menu_nama,
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

        $cekValidasi = $this->msMenusService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $data = [
            'menu_kode' => $request->menu_kode,
            'menu_nama' => $request->menu_nama,
            'menu_type' => $request->menu_type,
            'menu_link' => $request->menu_link,
            'menu_ikon' => $request->menu_ikon,
            'menu_status' => $request->menu_status,
            'parent_menu_id' => $request->parent_menu_id,
        ];

        if ($request->act == 'edit') {
            $res = $this->msMenusService->edit($request->menu_id, $data);
        } else {
            $res = $this->msMenusService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->msMenusService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->msMenusService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->msMenusService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "menu_id" => $dt->menu_id,
                "menu_kode" => $dt->menu_kode,
                "menu_nama" => $dt->menu_nama,
                "menu_type" => $dt->menu_type,
                "menu_link" => $dt->menu_link,
                "menu_ikon" => $dt->menu_ikon,
                "menu_status" => $dt->menu_status,
                "parent_menu_id" => $dt->parent_menu_id,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }

    public function getParent(): JsonResponse
    {
        $res = $this->msMenusService->getOptParent();
        if (!$res["status"]) {
            $res["data"] = [];
            return response()->json($res);
        }
        return response()->json($res);
    }
}
