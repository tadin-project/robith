<?php

namespace App\Services\Impl;

use App\Models\MsDimensi;
use App\Models\MsKriteria;
use App\Services\MsKriteriaService;
use Illuminate\Support\Facades\DB;

class MsKriteriaServiceImpl implements MsKriteriaService
{
    /**
     * @param $id
     * @return array
     */
    private function __cekData($id): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $dt = MsKriteria::find($id);
            if (!$dt) {
                $res = [
                    'status' => false,
                    'msg' => 'Data tidak ditemukan!',
                ];
            }
            $res['data'] = $dt;
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
                            count(mk.mk_id) as total
                        from
                            ms_kriteria mk
                        left join (
                            select
                                sum(msk_bobot) as tot_bobot,
                                mk_id
                            from
                                ms_sub_kriteria
                            where
                                msk_status = true
                            group by
                                mk_id ) msk on
                            msk.mk_id = mk.mk_id
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
                    "mk.mk_id",
                    "mk.mk_kode",
                    "mk.mk_nama",
                    "mk.mk_color",
                    "mk.mk_status",
                    "coalesce(msk.tot_bobot, 0) as tot_bobot",
                ];
            }

            $slc = implode(',', $cols);
            $qdata = "SELECT
                            $slc
                        from
                            ms_kriteria mk
                        left join (
                            select
                                sum(msk_bobot) as tot_bobot,
                                mk_id
                            from
                                ms_sub_kriteria
                            where
                                msk_status = true
                            group by
                                mk_id ) msk on
                            msk.mk_id = mk.mk_id
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
     * @param $req
     * @return array
     */
    public function validateData($req): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            if (gettype($req) == "array") {
                if ($req['act'] != 'add' && $req['act'] != 'edit') {
                    $res = [
                        'status' => false,
                        'msg' => 'Request tidak dikenal!',
                    ];
                    return $res;
                }

                if (empty($req['mk_kode']) || empty($req['mk_nama']) || empty($req['md_id'])) {
                    $res = [
                        'status' => false,
                        'msg' => 'Kode, nama, dan dimensi tidak boleh kosong!',
                    ];
                    return $res;
                }
            } else {
                if ($req->act != 'add' && $req->act != 'edit') {
                    $res = [
                        'status' => false,
                        'msg' => 'Request tidak dikenal!',
                    ];
                    return $res;
                }

                if (empty($req->mk_kode) || empty($req->mk_nama) || empty($req->md_id)) {
                    $res = [
                        'status' => false,
                        'msg' => 'Kode, nama, dan dimensi tidak boleh kosong!',
                    ];
                    return $res;
                }
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
            $dt = MsKriteria::create($data);
            if (!isset($dt->mk_id)) {
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
     * @param array $data
     * @return array
     */
    public function edit($id, array $data): array
    {
        $res = [
            'status' => true,
            'msg' => '',
        ];

        try {
            $cekData = $this->__cekData($id);
            if (!$cekData['status']) {
                return $cekData;
            }

            $dt = $cekData['data'];

            $dt->mk_kode = $data["mk_kode"];
            $dt->mk_nama = $data["mk_nama"];
            $dt->mk_color = $data["mk_color"];
            $dt->mk_desc = $data["mk_desc"];
            if (!is_null($data["mk_status"])) {
                $dt->mk_status = $data["mk_status"];
            }

            $d = $dt->save();
            if ($d <= 0) {
                $res = [
                    'status' => false,
                    'msg' => 'Gagal update data. Silahkan hubungi Admin!',
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
            $cekData = $this->__cekData($id);
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

    /**
     * @param string $act
     * @param mixed $key
     * @param mixed $val
     * @param string $old
     * @return array
     */
    public function checkDuplicate(string $act, $key, $val, string $old = ""): string
    {
        $res = "true";

        try {
            // DB::enableQueryLog();
            if (gettype($key) == "array") {
                $val = explode(",", $val);
                $dt = MsKriteria::where($key[0], $val[0]);
                for ($i = 1; $i < count($key); $i++) {
                    $dt = $dt->where($key[$i], $val[$i]);
                }

                if ($act == 'edit') {
                    $dt = $dt->where($key[0], "!=", $old);
                }
            } else {
                $dt = MsKriteria::where($key, $val);

                if ($act == 'edit') {
                    $dt = $dt->where($key, "!=", $old);
                }
            }

            // $dt = $dt->get();
            // dd($dt->toSql());
            // dd($dt, DB::getQueryLog());

            // $res = "false";
            if ($dt->count() > 0) {
                $res = "false";
            }
        } catch (\Throwable $th) {
            $res = "false";
        }

        return $res;
    }

    /**
     * @param $id
     * @return array
     */
    public function getById($id): array
    {
        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            $res = $this->__cekData($id);
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * @return array
     */
    public function getDimensi(): array
    {
        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            $res["data"] = MsDimensi::where("md_status", true)->orderBy("md_kode")->get();
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }
}
