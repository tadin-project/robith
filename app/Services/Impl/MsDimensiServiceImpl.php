<?php

namespace App\Services\Impl;

use App\Models\MsDimensi;
use App\Services\MsDimensiService;
use Illuminate\Support\Facades\DB;

class MsDimensiServiceImpl implements MsDimensiService
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
            $dt = MsDimensi::find($id);
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
                            count(md.md_id) as total
                        from
                            ms_dimensi md
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
                    "md.md_id",
                    "md.md_kode",
                    "md.md_nama",
                    "md.md_status",
                ];
            }

            $slc = implode(',', $cols);
            $qdata = "SELECT
                            $slc
                        from
                            ms_dimensi md
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

                if (empty($req['md_kode']) || empty($req['md_nama'])) {
                    $res = [
                        'status' => false,
                        'msg' => 'Kode dan nama tidak boleh kosong!',
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

                if (empty($req->md_kode) || empty($req->md_nama)) {
                    $res = [
                        'status' => false,
                        'msg' => 'Kode dan nama tidak boleh kosong!',
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
            $dt = MsDimensi::create($data);
            if (!isset($dt->md_id)) {
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

            $dt->md_kode = $data["md_kode"];
            $dt->md_nama = $data["md_nama"];
            if (!is_null($data["md_status"])) {
                $dt->md_status = $data["md_status"];
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
     * @param string $key
     * @param string $val
     * @param string $old
     * @return array
     */
    public function checkDuplicate(string $act, string $key, string $val, string $old = ""): string
    {
        $res = "true";

        try {
            $dt = MsDimensi::where($key, $val);
            if ($act == 'edit') {
                $dt = $dt->where($key, "!=", $old);
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
}
