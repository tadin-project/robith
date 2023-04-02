<?php

namespace App\Services\Impl;

use App\Models\MsKriteria;
use App\Models\MsSubKriteria;
use App\Services\MsSubKriteriaService;
use Illuminate\Support\Facades\DB;

class MsSubKriteriaServiceImpl implements MsSubKriteriaService
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
            $dt = MsSubKriteria::find($id);
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
                            count(msk.msk_id) as total
                        from
                            ms_sub_kriteria msk
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
                    "msk.msk_id",
                    "msk.msk_kode",
                    "msk.msk_nama",
                    "msk.msk_bobot",
                    "msk.msk_status",
                    "msk.mk_id",
                ];
            }

            $slc = implode(',', $cols);
            $qdata = "SELECT
                            $slc
                        from
                            ms_sub_kriteria msk
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

                if (empty($req['msk_kode']) || empty($req['msk_nama']) || empty($req['mk_id'])) {
                    $res = [
                        'status' => false,
                        'msg' => 'Kode, nama, dan kriteria tidak boleh kosong!',
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

                if (empty($req->msk_kode) || empty($req->msk_nama) || empty($req->mk_id)) {
                    $res = [
                        'status' => false,
                        'msg' => 'Kode, nama, dan kriteria tidak boleh kosong!',
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
            $dt = MsSubKriteria::create($data);
            if (!isset($dt->msk_id)) {
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

            $dt->msk_kode = $data["msk_kode"];
            $dt->msk_nama = $data["msk_nama"];
            $dt->msk_bobot = $data["msk_bobot"];
            $dt->mk_id = $data["mk_id"];
            if (!is_null($data["msk_status"])) {
                $dt->msk_status = $data["msk_status"];
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
            if (gettype($key) == "array") {
                $val = explode(",", $val);
                $dt = MsSubKriteria::where($key[0], $val[0]);
                for ($i = 1; $i < count($key); $i++) {
                    $dt = $dt->where($key[$i], $val[$i]);
                }
                if ($act == 'edit') {
                    $dt = $dt->where($key[0], "!=", $old);
                }
            } else {
                $dt = MsSubKriteria::where($key, $val);
                if ($act == 'edit') {
                    $dt = $dt->where($key, "!=", $old);
                }
            }

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
     * @param $id
     * @return array
     */
    public function getKriteria(): array
    {
        $res = [
            'status' => true,
            'msg' => "",
        ];

        try {
            $res["data"] = MsKriteria::where("mk_status", true)->orderBy("mk_kode", "asc")->get();
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
            ];
        }

        return $res;
    }
}
