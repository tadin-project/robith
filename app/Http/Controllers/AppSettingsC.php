<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MyC;
use App\Models\AppSettings;
use App\Services\AppSettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppSettingsC extends MyC
{
    private AppSettingsService $appSettingsService;
    private String $dirLogo = "assets/img/logo";
    private String $dirBackground = "assets/img/background";
    private String $dirGambar = "assets/img";

    public function __construct(AppSettingsService $appSettingsService)
    {
        parent::__construct();
        $this->middleware("has_akses:app-settings");
        $this->appSettingsService = $appSettingsService;
    }

    public function index(): View
    {

        $data = [
            "__title" => "Master Setting Aplikasi",
        ];

        return $this->my_view("v_app_setting", $data);
    }

    public function getData(Request $request): JsonResponse
    {
        $cols = [
            "as_id",
            "as_nama",
            "as_value",
            "as_jenis",
            "as_key",
        ];

        $colsSearch = [
            "as_value",
            "as_nama",
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
        $getTotal = $this->appSettingsService->getTotal($sWhere);
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

        $getData = $this->appSettingsService->getData($sWhere, $sOrder, $sLimit, $cols);
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

        $aksi = "";

        foreach ($detailData as $v) {

            $id = $v->as_id;

            $aksiEdit = '<a href="javascript:void(0)" class="btn btn-sm btn-primary mb-1 mx-1" title="Edit" onclick="fnEdit(\'' . $id . '\')"><i class="fas fa-pencil-alt"></i></a>';

            $aksi = $aksiEdit;

            $asValue = $v->as_value;
            if ($v->as_jenis == 2) {
                $url_gambar = "";
                if ($v->as_key == 'background_auth') {
                    $url_gambar = asset('') . $this->dirBackground . '/' . $v->as_value;
                } else if ($v->as_key == 'app_logo') {
                    $url_gambar = asset('') . $this->dirLogo . '/' . $v->as_value;
                } else {
                    $url_gambar = asset('')  . $this->dirGambar . '/' . $v->as_value;
                }

                $asValue = '<a href="' . $url_gambar . '" target="_blank">' . $v->as_value . '</a>';
            }

            $data['data'][] = [
                $no,
                $v->as_nama,
                $asValue,
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

        $cekValidasi = $this->appSettingsService->validateData($request);
        if (!$cekValidasi['status']) {
            return response()->json($cekValidasi);
        }

        $as_jenis = $request->as_jenis;
        $as_id = $request->as_id;

        $data = [];

        if ($as_jenis == 2) {
            $oldAsData = AppSettings::where("as_id", $as_id)->get();
            if ($oldAsData->count() > 0) {
                $currentOldAsData = $oldAsData[0];
                if (!empty($currentOldAsData->as_value)) {
                    $url_gambar = "";
                    if ($currentOldAsData->as_key == 'background_auth') {
                        $url_gambar = './' . $this->dirBackground . '/' . $currentOldAsData->as_value;
                    } else if ($currentOldAsData->as_key == 'app_logo') {
                        $url_gambar = './' . $this->dirLogo . '/' . $currentOldAsData->as_value;
                    } else {
                        $url_gambar = './'  . $this->dirGambar . '/' . $currentOldAsData->as_value;
                    }
                }
                $this->__removeFile($url_gambar);
            }

            if ($request->file('as_value')) {
                $file = $request->file('as_value');
                $fileName = "";
                $dir = "";
                if ($currentOldAsData->as_key == 'background_auth') {
                    $fileName = "background";
                    $dir = $this->dirBackground;
                } else if ($currentOldAsData->as_key == 'app_logo') {
                    $fileName = "logo";
                    $dir = $this->dirLogo;
                } else {
                    $fileName = $file->getClientOriginalName();
                    $dir = $this->dirGambar;
                }
                $fileName = time() . rand(1, 99) . '_' . $fileName . '.' . $file->extension();
                $file->move(public_path($dir), $fileName);
                $data["as_value"] = $fileName;
            }
        } else {
            $data["as_value"] = $request->as_value;
        }

        $res = $this->appSettingsService->edit($request->as_id, $data);

        return response()->json($res);
    }

    public function getById($id): JsonResponse
    {
        $res = $this->appSettingsService->getById($id);
        if ($res['status']) {
            $dt = $res["data"];
            $asValue = $dt->as_value;
            if ($dt->as_jenis == 2) {
                if ($dt->as_key == 'background_auth') {
                    $asValue = asset('') . $this->dirBackground . '/' . $dt->as_value;
                } else if ($dt->as_key == 'app_logo') {
                    $asValue = asset('') . $this->dirLogo . '/' . $dt->as_value;
                } else {
                    $asValue = asset('') . $dt->as_value;
                }
            }
            $data = [
                "as_id" => $dt->as_id,
                "as_jenis" => $dt->as_jenis,
                "as_nama" => $dt->as_nama,
                "as_desc" => $dt->as_desc,
                "as_value" => $asValue,
            ];
            $res["data"] = $data;
        }
        return response()->json($res);
    }

    private function __removeFile($file)
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
