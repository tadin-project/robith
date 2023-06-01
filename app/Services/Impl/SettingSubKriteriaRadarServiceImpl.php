<?php

namespace App\Services\Impl;

use App\Models\MsDimensi;
use App\Models\MsKriteria;
use App\Models\MsSubKriteria;
use App\Models\SettingSubKriteriaRadar;
use App\Services\SettingSubKriteriaRadarService;
use Illuminate\Support\Facades\DB;

class SettingSubKriteriaRadarServiceImpl implements SettingSubKriteriaRadarService
{
    /**
     * @param string $where
     * @return array
     */
    public function getTotal(string $where = ""): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $qtotal = "SELECT
                            count(mr.mr_id) as total
                        from
                            ms_radar mr
                        where
                            mr.mr_status = true $where";
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
                    "mr.mr_id",
                    "mr.mr_kode",
                    "mr.mr_nama",
                ];
            }

            $slc = implode(',', $cols);
            $qdata = "SELECT
                            $slc
                        from
                            ms_radar mr
                        where
                            mr.mr_status = true
                            $where
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

    public function getDimensi(): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $data = MsDimensi::where('md_status', true)->orderBy('md_nama', "asc")->get();
            $res['data'] = $data;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    public function getKriteria(string $md_id): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $rawData = MsKriteria::where('mk_status', true)->where('md_id', $md_id)->orderBy('mk_kode', "asc")->get();
            $data = [];
            if ($rawData->count() > 0) {
                foreach ($rawData as $v) {
                    $data[] = [
                        'id' => $v->mk_id,
                        'nama' => $v->mk_nama,
                        'kode' => $v->mk_kode,
                    ];
                }
            }
            $res['data'] = $data;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    public function getSubKriteria(string $mk_id): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $rawData = MsSubKriteria::where('msk_status', true)->where('mk_id', $mk_id)->orderBy('msk_kode', "asc")->get();
            $data = [];
            if ($rawData->count() > 0) {
                foreach ($rawData as $v) {
                    $data[] = [
                        'id' => $v->msk_id,
                        'nama' => $v->msk_nama,
                        'kode' => $v->msk_kode,
                    ];
                }
            }

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
            SettingSubKriteriaRadar::insert($data);
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @param string $msk_id
     * @param arrau $mr_id
     * @return array
     */
    public function del(string $msk_id, array $mr_id): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            SettingSubKriteriaRadar::where('msk_id', $msk_id)->whereIn('mr_id', $mr_id)->delete();
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }
}
