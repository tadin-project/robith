<?php

namespace App\Services\Impl;

use App\Models\SettingSubKriteriaRadar;
use App\Services\SettingSubKriteriaRadarService;
use Illuminate\Support\Facades\DB;

class SettingSubKriteriaRadarServiceImpl implements SettingSubKriteriaRadarService
{
    /**
     * @param string $where
     * @return array
     */
    public function getTotal(string $where): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $qtotal = "SELECT
                            count(sskr.sskr_id) as total
                        from
                            setting_sub_kriteria_radar sskr
                        where
                            0 = 0 $where";
            $total = DB::select($qtotal);
            $res['total'] = $total[0]->total;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param array $cols
     * @return array
     */
    public function getData(string $where = "", string $order = "", string $limit = "", array $cols = []): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            if (count($cols) == 0) {
                $cols = [
                    "sskr.sskr_id",
                    "sskr.sskr_kode",
                    "sskr.sskr_nama",
                    "sskr.sskr_status",
                ];
            }

            $slc = implode(',', $cols);
            $qdata = "SELECT
                            $slc
                        from
                            setting_sub_kriteria_radar sskr
                        where
                            0 = 0 $where
                        $order $limit";
            $data = DB::select($qdata);
            $res['data'] = $data;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param array $data
     * @return array
     */
    public function add(array $data): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $dt = SettingSubKriteriaRadar::create($data);
            if (!isset($dt->sskr_id)) {
                $res = [
                    'status' => false,
                    'msg' => 'Data gagal ditambahkan. Silahkan hubungi Admin!',
                ];
            }
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param $id
     * @return array
     */
    public function del($id): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            // $cekData = $this->__cekData($id);
            $cekData = [];
            if (!$cekData['status']) {
                return $cekData;
            }

            $d = $cekData['data']->delete();
            if ($d < 0) {
                $res = [
                    'status' => false,
                    'msg' => 'Gagal hapus data. Silahkan hubungi Admin!',
                ];
            }
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }
}
