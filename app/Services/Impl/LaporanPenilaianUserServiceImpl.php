<?php

namespace App\Services\Impl;

use App\Models\MsSubKriteria;
use App\Services\LaporanPenilaianUserService;
use Illuminate\Support\Facades\DB;

class LaporanPenilaianUserServiceImpl implements LaporanPenilaianUserService
{
    public function getKriteriaData(string $user_id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $data = [];
            $sqlKriteria = "SELECT
                                mk.*
                            from
                                ms_kriteria mk
                            inner join ms_dimensi md on
                                md.md_id = mk.md_id
                            where
                                mk.mk_status
                                and md.md_status = true
                            order by
                                md.md_kode ,
                                mk.mk_kode";
            $rawKriteria = DB::select($sqlKriteria);
            if (count($rawKriteria) > 0) {
                foreach ($rawKriteria as $k => $v) {
                    $sqlNilaiKriteria = "SELECT
                        sum((coalesce(ad.asd_final,0) * coalesce(mr.mr_bobot,0) / 100) * coalesce(msk.msk_bobot,0) / 100 ) as total
                    from
                        asesmen_detail ad
                    inner join asesmen a on
                        a.as_id = ad.as_id
                        and a.as_status = 2
                    inner join tenant t on
                        t.tenant_id = a.tenant_id
                        and t.user_id = $user_id
                    inner join setting_sub_kriteria_radar sskr on
                        sskr.sskr_id = ad.sskr_id
                    inner join ms_radar mr on
                        mr.mr_id = sskr.mr_id
                        and mr.mr_status = true
                    inner join ms_sub_kriteria msk on
                        msk.msk_id = sskr.msk_id
                        and msk.mk_id = $v->mk_id
                        and msk.msk_status = true";
                    $rawNilaiKriteria = DB::select($sqlNilaiKriteria)[0]->total;

                    $banyakSubKriteria = MsSubKriteria::where("mk_id", $v->mk_id)->where("msk_status", true)->count();

                    $rata2 = $rawNilaiKriteria / $banyakSubKriteria;

                    $data[] = [
                        "mk_nama" => $v->mk_nama,
                        "rata2" => $rata2,
                    ];
                }
            }

            $res["data"] = $data;
        } catch (\Throwable $th) {
            $res = [
                "status" => true,
                "msg" => $th->getMessage(),
                "stack" => env("DEV_MODE", "production") == "production" ? null : $th->getTrace(),
            ];
        }

        return $res;
    }

    public function cetak(string $user_id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $data = [];
            $sqlKriteria = "SELECT
                                mk.*
                            from
                                ms_kriteria mk
                            inner join ms_dimensi md on
                                md.md_id = mk.md_id
                            where
                                mk.mk_status
                                and md.md_status = true
                            order by
                                md.md_kode ,
                                mk.mk_kode";
            $rawKriteria = DB::select($sqlKriteria);
            if (count($rawKriteria) > 0) {
                $kriteria = [];
                foreach ($rawKriteria as $k => $v) {
                    $kriteria[$k] = [
                        "nama" => $v->mk_nama,
                        "total" => 0,
                        "rata2" => 0,
                        "subKriteria" => [],
                    ];

                    $rata2 = 0;
                    $nilaiKriteria = 0;
                    $subKriteria = [];
                    $banyakSubKriteria = 0;

                    $rawSubKriteria = MsSubKriteria::where("mk_id", $v->mk_id)->where("msk_status", true)->orderBy("msk_kode", "asc")->get();
                    if ($rawSubKriteria->count() > 0) {
                        $banyakSubKriteria = $rawSubKriteria->count();

                        $nilaiSubKriteria = 0;
                        foreach ($rawSubKriteria as $k2 => $v2) {
                            $subKriteria[$k2] = [
                                "nama" => $v2->msk_nama,
                                "total" => 0,
                                "radar" => [],
                            ];

                            $nilaiSubKriteria = 0;
                            $radar = [];

                            // mengambil data radar
                            $sqlRadar = "SELECT
                                            mr.mr_nama ,
                                            mr.mr_bobot ,
                                            ad.asd_final
                                        from
                                            asesmen_detail ad
                                        inner join asesmen a on
                                            a.as_id = ad.as_id
                                        inner join tenant t on
                                            t.tenant_id = a.tenant_id
                                            and t.user_id = $user_id
                                        inner join setting_sub_kriteria_radar sskr on
                                            sskr.sskr_id = ad.sskr_id
                                            and sskr.msk_id = $v2->msk_id
                                        inner join ms_radar mr on
                                            mr.mr_id = sskr.mr_id
                                            and mr.mr_status = true
                                        order by
                                            mr.mr_kode 
                                        ;";

                            $rawRadar = DB::select($sqlRadar);
                            if (count($rawRadar) > 0) {
                                $nilaiRadar = 0;
                                foreach ($rawRadar as $k3 => $v3) {
                                    $nilaiRadar = floatval($v3->mr_bobot) * ($v3->asd_final) / 100;
                                    $radar[$k3] = [
                                        "nama" => $v3->mr_nama,
                                        "total" => $nilaiRadar,
                                    ];

                                    $nilaiSubKriteria += $nilaiRadar * floatval($v2->msk_bobot) / 100;
                                }
                            }

                            $subKriteria[$k2]["total"] = $nilaiSubKriteria;
                            $subKriteria[$k2]["radar"] = $radar;

                            $nilaiKriteria += $nilaiSubKriteria;
                        }

                        $rata2 = $nilaiKriteria / $banyakSubKriteria;
                    }

                    $kriteria[$k]["total"] = $nilaiKriteria;
                    $kriteria[$k]["rata2"] = $rata2;
                    $kriteria[$k]["subKriteria"] = $subKriteria;
                }
                $data = $kriteria;
            }

            $res["data"] = $data;
        } catch (\Throwable $th) {
            $res = [
                "status" => true,
                "msg" => $th->getMessage(),
                "stack" => env("DEV_MODE", "production") == "production" ? null : $th->getTrace(),
            ];
        }

        return $res;
    }
}
