<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MyC;
use App\Services\MsGroupsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MsGroupsC extends MyC
{
    private MsGroupsService $msGroupsService;
    public function __construct(MsGroupsService $msGroupsService)
    {
        parent::__construct();
        $this->middleware("has_akses:ms-groups");
        $this->msGroupsService = $msGroupsService;
    }

    public function index(): View
    {
        $data = [
            "__title" => "Master Hak Akses",
        ];

        return $this->my_view("v_ms_group", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            'mg.group_id',
            'mg.group_kode',
            'mg.group_nama',
            'mg.group_status',
            'coalesce(gm.tot_menu,0) as tot_menu',
        ];

        $colsSearch = [
            'mg.group_nama',
            'mg.group_kode',
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
        $getTotal = $this->msGroupsService->getTotal($sWhere);
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

        $getData = $this->msGroupsService->getData($sWhere, $sOrder, $sLimit, $cols);
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

            if ($v->group_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->group_id;

            $isHaveAkses = 'secondary';
            if ($v->tot_menu > 0) {
                $isHaveAkses = 'warning';
            }

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiAkses = '<a href="javascript:void(0)" class="btn btn-sm btn-' . $isHaveAkses . ' mb-1 mx-1" title="Setting Hak Akses" onclick="fnAkses(\'' . $id . '\',\'' . $v->group_nama . '\')"><i class="fas fa-cogs"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->group_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";

            if ($id == 1) {
                if ($this->__sess_user['group_id'] == 1) {
                    $aksi .= $aksiEdit . $aksiAkses;
                } else {
                    continue;
                }
            } else if ($id == 2) {
                if ($this->__sess_user['group_id'] == 1 || $this->__sess_user['group_id'] == 2) {
                    $aksi .= $aksiEdit . $aksiAkses;
                } else {
                    $aksi = '';
                }
            } else {
                $aksi .= $aksiEdit . $aksiAkses . $aksiHapus;
            }

            $data['data'][] = [
                $no,
                $v->group_kode,
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

        $cekValidasi = $this->msGroupsService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $data = [
            'group_kode' => $request->group_kode,
            'group_nama' => $request->group_nama,
            'group_status' => $request->group_status,
        ];

        if ($request->act == 'edit') {
            $res = $this->msGroupsService->edit($request->group_id, $data);
        } else {
            $res = $this->msGroupsService->add($data);
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $res = $this->msGroupsService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->msGroupsService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->msGroupsService->getById($id);
        return response()->json($res);
    }

    public function getAkses(Request $request): JsonResponse
    {
        $res = $this->msGroupsService->getAkses($request->group_id, $request->parent_menu_id);
        if (!$res['status']) {
            return response()->json([]);
        }
        return response()->json($res['data']);
    }

    public function saveAkses(Request $request): JsonResponse
    {
        $res = $this->msGroupsService->saveAkses($request->group_id, $request->menu_id);
        return response()->json($res);
    }
}
