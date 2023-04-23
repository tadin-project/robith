<?php

namespace App\Services\Impl;

use App\Models\Asesmen;
use App\Models\AsesmenDetail;
use App\Models\MsKategoriUsaha;
use App\Models\MsKriteria;
use App\Services\ValidasiAsesmenService;
use Illuminate\Support\Facades\DB;

/**
 * Summary of ValidasiAsesmenServiceImpl
 */
class ValidasiAsesmenServiceImpl implements ValidasiAsesmenService
{
    public function getKategoriUsaha(): array
    {

        $res = [
            "status" => true,
            "msg" => "",
        ];
        try {
            $res["data"] = MsKategoriUsaha::where("mku_status", true)->orderBy("mku_nama", "asc")->get();
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    /**
     * Summary of getTotal
     * @param string $where
     * @return array
     */
    public function getTotal(string $where): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];
        try {
            $sql = "SELECT
                        count(a.as_id) total
                    from
                        asesmen a
                    inner join tenant t on
                        t.tenant_id = a.tenant_id
                    where
                        0 = 0
                        $where";
            $dt =  DB::select($sql);
            $res["total"] = $dt[0]->total;
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    /**
     * Summary of getData
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param array $columns
     * @return array
     */
    public function getData(string $where, string $order, string $limit, array $columns): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];
        try {
            if (count($columns) <= 0) {
                $columns = [
                    "a.as_id",
                    "a.created_at",
                    "t.tenant_nama",
                ];
            }

            $slc = implode(",", $columns);

            $sql = "SELECT
                        $slc
                    from
                        asesmen a
                    inner join tenant t on
                        t.tenant_id = a.tenant_id
                    where
                        0 = 0
                        $where
                    $order $limit";
            $dt =  DB::select($sql);
            $res["data"] = $dt;
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    public function getKriteria(): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];
        try {
            $mk = MsKriteria::where("mk_status", true)->orderBy("mk_kode", "asc")->get();
            $res["data"] = [];
            if ($mk->count() > 0) {
                $dtMk = [];
                foreach ($mk as $k => $v) {
                    $dtMk[$k] = [
                        "mk_id" => $v->mk_id,
                        "mk_nama" => $v->mk_nama,
                        "msk" => [],
                    ];

                    $msk = $v->subKriteria()->where("msk_status", true)->orderBy("msk_kode", "asc")->get();
                    if ($msk->count() > 0) {
                        foreach ($msk as $k1 => $v1) {
                            $dtMk[$k]["msk"][$k1] = [
                                "msk_id" => $v1->msk_id,
                                "msk_nama" => $v1->msk_nama,
                                "msk_is_submission" => $v1->msk_is_submission,
                            ];
                        }
                    }
                }
                $res["data"] = $dtMk;
            }
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    public function edit(string $id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $dt = Asesmen::find($id);
            if (!isset($dt->as_id)) {
                $res = [
                    "status" => false,
                    "msg" => "Data tidak ditemukan!",
                ];
            }

            $res["data"] = [
                "as_id" => $dt->as_id,
                "as_status" => $dt->as_status,
                "tenant_nama" => $dt->tenant->tenant_nama,
                "tenant_desc" => $dt->tenant->tenant_desc,
                "asd" => [],
            ];

            if ($dt->asesmenDetail->count() > 0) {
                $detail = [];
                foreach ($dt->asesmenDetail as $k => $v) {
                    $detail[$k] = [
                        "asd_id" => $v->asd_id,
                        "msk_id" => $v->msk_id,
                        "msk_is_submission" => $v->subKriteria->msk_is_submission,
                        "asd_value" => $v->asd_status == 2 ? 0 : $v->asd_value,
                        "asd_file" => $v->asd_file,
                        "asd_status" => $v->asd_status,
                        "user_fullname" => $v->asd_status != 0 ? $v->user->user_fullname : "",
                    ];
                }

                $res["data"]["asd"] = $detail;
            }
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    public function update(string $id, array $data): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $cekDetail = AsesmenDetail::where("as_id", $id)->where("asd_status", 0);
            if ($cekDetail->count() > 0) {
                $res = [
                    "status" => false,
                    "msg" => "Data tidak dapat difinalisasi karena ada beberapa data yang masih belum divalidasi!",
                ];
                return $res;
            }
            Asesmen::find($id)->update($data);
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }

    public function updateDetail(string $id, array $data): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            AsesmenDetail::find($id)->update($data);
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }
}
