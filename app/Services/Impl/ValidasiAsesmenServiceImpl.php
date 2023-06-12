<?php

namespace App\Services\Impl;

use App\Models\Asesmen;
use App\Models\AsesmenDetail;
use App\Models\ConvertionValue;
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
                        $dtMr = [];
                        foreach ($msk as $k1 => $v1) {
                            $dtMr = [];
                            $sqlMr = "SELECT mr.*, sskr.sskr_id from ms_radar mr inner join setting_sub_kriteria_radar sskr on sskr.mr_id = mr.mr_id where mr.mr_status = true and sskr.msk_id = $v1->msk_id order by mr.mr_kode";
                            $rawMr = DB::select($sqlMr);
                            if (count($rawMr) > 0) {
                                foreach ($rawMr as $k2 => $v2) {
                                    $dtMr[] = [
                                        "sskr_id" => $v2->sskr_id,
                                        "mr_id" => $v2->mr_id,
                                        "mr_nama" => $v2->mr_nama,
                                    ];
                                }
                            }

                            $dtMk[$k]["msk"][$k1] = [
                                "msk_id" => $v1->msk_id,
                                "msk_nama" => $v1->msk_nama,
                                "msk_is_submission" => $v1->msk_is_submission,
                                "radar" => $dtMr,
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

            $nilaiMin = DB::select("SELECT min(cv.cval_nilai) nilai_min from convertion_value cv where cv.cval_status = true")[0]->nilai_min;

            $sqlGetAsesmenDetail =
                "SELECT
                    ad.*,
                    msk.msk_is_submission ,
                    mu.user_fullname ,
                    cv.cval_nama
                from
                    asesmen_detail ad
                inner join setting_sub_kriteria_radar sskr on
                    sskr.sskr_id = ad.sskr_id
                inner join ms_sub_kriteria msk on
                    msk.msk_id = sskr.msk_id
                left join ms_users mu on
                    mu.user_id = ad.user_id
                left join convertion_value cv on
                    cv.cval_nilai = ad.asd_final
                where
                    ad.as_id = $dt->as_id";
            $rawDataAsesmenDetail = DB::select($sqlGetAsesmenDetail);

            if (count($rawDataAsesmenDetail) > 0) {
                $detail = [];
                foreach ($rawDataAsesmenDetail as $k => $v) {
                    $detail[$k] = [
                        "asd_id" => $v->asd_id,
                        "sskr_id" => $v->sskr_id,
                        "msk_is_submission" => $v->msk_is_submission,
                        "asd_value" => $v->asd_status > 0 ? $v->asd_final : $v->asd_value,
                        "asd_status" => $v->asd_status,
                        "user_fullname" => $v->asd_status != 0 ? $v->user_fullname : "",
                        "cval_nama" => $v->cval_nama,
                    ];
                }

                $res["data"]["detail"] = $detail;
            }

            $sqlGetAsesmenFile =
                "SELECT
                    af.*
                from
                    asesmen_file af
                where
                    af.as_id = $dt->as_id";
            $rawDataAsesmenFile = DB::select($sqlGetAsesmenFile);

            if (count($rawDataAsesmenFile) > 0) {
                $file = [];
                foreach ($rawDataAsesmenFile as $k => $v) {
                    $file[$k] = [
                        "asf_id" => $v->asf_id,
                        "msk_id" => $v->msk_id,
                        "asf_file" => $v->asf_file,
                    ];
                }

                $res["data"]["file"] = $file;
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

            // $sqlDetail = "SELECT
            //                     ad.asd_final ,
            //                     ad.sskr_id ,
            //                     mr.mr_bobot ,
            //                     msk.msk_bobot  
            //                 from
            //                     asesmen_detail ad
            //                 inner join setting_sub_kriteria_radar sskr on
            //                     sskr.sskr_id = ad.sskr_id
            //                 inner join ms_radar mr on
            //                     mr.mr_id = sskr.mr_id
            //                 inner join ms_sub_kriteria msk on
            //                     msk.msk_id = sskr.msk_id 
            //                 where
            //                     ad.as_id = $id";

            // $dataDetail = DB::select($sqlDetail);
            // $as_total = 0;
            // $as_max = 0;
            // if (count($dataDetail) > 0) {
            //     foreach ($dataDetail as $k => $v) {
            //         $subKomponen = $v->mr_bobot * $v->asd_final / 100;
            //         $subTotal = floatval($subKomponen) * floatval($v->msk_bobot) / 100;
            //         $as_total += $subTotal;
            //         $as_max += floatval($v->msk_bobot);
            //     }
            // }

            $as_total = 0;
            $as_max = 0;

            $sqlSubKriteria = "SELECT
                                    msk_id,
                                    msk_bobot 
                                from
                                    ms_sub_kriteria msk
                                where
                                    msk.msk_status = 1
                                order by
                                    msk.mk_id ,
                                    msk.msk_kode";
            $rawSubKriteria = DB::select($sqlSubKriteria);

            if (count($rawSubKriteria) > 0) {
                $sqlDetail = "";
                foreach ($rawSubKriteria as $k => $v) {
                    $sqlDetail = "SELECT
                                    ad.asd_final ,
                                    mr.mr_bobot ,
                                    sskr.sskr_id ,
                                    sskr.mr_id  ,
                                    sskr.msk_id 
                                from
                                    asesmen_detail ad
                                inner join setting_sub_kriteria_radar sskr on
                                    sskr.sskr_id = ad.sskr_id
                                inner join ms_radar mr on
                                    mr.mr_id = sskr.mr_id
                                where
                                    ad.as_id = $id
                                    and sskr.msk_id = $v->msk_id
                                    and mr.mr_status = true";
                    $rawSqlDetail = DB::select($sqlDetail);
                    if (count($rawSqlDetail) > 0) {

                        foreach ($rawSqlDetail as $k1 => $v1) {
                            $subKomponen = $v1->mr_bobot * $v1->asd_final / 100;
                            $subTotal = floatval($subKomponen) * floatval($v->msk_bobot) / 100;
                            $as_total += $subTotal;
                        }
                    }

                    $as_max += floatval($v->msk_bobot);
                }
            }

            $data["as_total"] = $as_total;
            $data["as_max"] = $as_max;

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

    public function getConvertionValue(): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $data = ConvertionValue::where("cval_status", true)->orderBy("cval_kode", "asc")->get();
            $res["data"] = $data;
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        return $res;
    }
}
