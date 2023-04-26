<?php

namespace App\Http\Controllers;

use App\Models\MsLampiran;
use App\Services\MsLampiranService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MsLampiranC extends MyC
{
    private MsLampiranService $msLampiranService;
    private $dirUploads = "uploads/lampiran";
    public function __construct(MsLampiranService $msLampiranService)
    {
        parent::__construct();
        $this->middleware("has_akses:ms-lampiran");
        $this->msLampiranService = $msLampiranService;
    }

    public function index(): View
    {
        $data = [
            "__title" => "Master Lampiran",
        ];

        return $this->my_view("v_ms_lampiran", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "ml.lampiran_id",
            "ml.lampiran_kode",
            "ml.lampiran_nama",
            "ml.lampiran_jenis",
            "ml.lampiran_field",
            "ml.lampiran_status",
        ];

        $colsSearch = [
            "ml.lampiran_kode",
            "ml.lampiran_nama",
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
        $getTotal = $this->msLampiranService->getTotal($sWhere);
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

        $getData = $this->msLampiranService->getData($sWhere, $sOrder, $sLimit, $cols);
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

        $jenis = "";
        $lampiran = "";
        $status = "";
        $aksi = "";

        foreach ($detailData as $v) {

            if ($v->lampiran_jenis == 1) {
                $jenis = "<span class='badge badge-success'>File</span>";
                $lampiran = '<a href="' . url($this->dirUploads) . '/' . $v->lampiran_field . '" target="_blank">' . $v->lampiran_field . '</a>';
            } else {
                $jenis = "<span class='badge badge-warning'>Link</span>";
                $lampiran = '<a href="' . $v->lampiran_field . '" target="_blank">' . $v->lampiran_nama . '</a>';
            }

            if ($v->lampiran_status == 1) {
                $status = "<span class='badge badge-success'>Aktif</span>";
            } else {
                $status = "<span class='badge badge-danger'>Non Aktif</span>";
            }

            $id = $v->lampiran_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';
            $aksiHapus = '<a href="javascript:void(0)" class="btn btn-sm btn-danger mb-1 mx-1" title="Hapus" onclick="fnDel(\'' . $id . '\',\'' . $v->lampiran_nama . '\')"><i class="fas fa-trash"></i></a>';

            $aksi = "";
            $aksi .= $aksiEdit . $aksiHapus;

            $data['data'][] = [
                $no,
                $v->lampiran_kode,
                $v->lampiran_nama,
                $jenis,
                $lampiran,
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

        $cekValidasi = $this->msLampiranService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $lampiran_jenis = $request->lampiran_jenis;

        $data = [
            'lampiran_nama' => $request->lampiran_nama,
            'lampiran_kode' => $request->lampiran_kode,
            'lampiran_status' => $request->lampiran_status,
            'lampiran_jenis' => $lampiran_jenis,
        ];

        if ($lampiran_jenis == 2) {
            $data["lampiran_field"] = $request->lampiran_field;
        }

        if ($request->act == 'edit') {
            $id = $request->lampiran_id;
            $res = $this->msLampiranService->edit($request->lampiran_id, $data);
        } else {
            $res = $this->msLampiranService->add($data);
            $id = $res["id"];
        }

        if (!$res["status"]) {
            return response()->json($res);
        }

        if ($lampiran_jenis == 1) {
            if ($request->file('lampiran_field')) {
                if ($request->act == "edit") {
                    $old_lampiran = MsLampiran::find($id);
                    if (isset($old_lampiran->lampiran_field)) {
                        $this->__hapusFile("./" . $this->dirUploads . "/" . $old_lampiran->lampiran_field);
                    }
                }

                $lampiran = $request->file('lampiran_field');
                $fileName = time() . "_" .  Str::random(15) . '.' . $lampiran->extension();
                $lampiran->move(public_path($this->dirUploads), $fileName);
                $data[] = $fileName;

                $res = $this->msLampiranService->edit($id, [
                    "lampiran_field" => $fileName,
                ]);
            } else {
                if ($request->act != "edit" || $request->has_file != 1) {
                    $res["status"] = false;
                    $res["msg"] = "Tidak ada file";
                    return response()->json($res);
                }
            }
        }

        return response()->json($res);
    }

    public function delete(int $id): JsonResponse
    {
        $old_data = $this->msLampiranService->getById($id);
        if ($old_data["status"]) {
            if ($old_data["data"]->lampiran_jenis == 1) {
                $this->__hapusFile("./" . $this->dirUploads . "/" . $old_data["data"]->lampiran_field);
            }
        }
        $res = $this->msLampiranService->del($id);

        return response()->json($res);
    }

    public function checkDuplicate(Request $request): string
    {
        $res = $this->msLampiranService->checkDuplicate($request->act, $request->key, $request->val, (!empty($request->old) ? $request->old : ""));
        return $res;
    }

    public function getById($id): JsonResponse
    {
        $res = $this->msLampiranService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $data = [
                "lampiran_id" => $dt->lampiran_id,
                "lampiran_nama" => $dt->lampiran_nama,
                "lampiran_kode" => $dt->lampiran_kode,
                "lampiran_status" => $dt->lampiran_status,
                "lampiran_jenis" => $dt->lampiran_jenis,
                "lampiran_field" => $dt->lampiran_field,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }

    private function __hapusFile(string $namaFile)
    {
        if (!empty($namaFile)) {
            if (file_exists($namaFile)) {
                unlink($namaFile);
            }
        }
    }
}
