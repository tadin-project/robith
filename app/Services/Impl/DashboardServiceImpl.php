<?php

namespace App\Services\Impl;

use App\Services\DashboardService;
use Illuminate\Support\Facades\DB;

class DashboardServiceImpl implements DashboardService
{
    public function getInitDataTenant(): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $kriteria = DB::select(
                "SELECT
                    mk.mk_id ,
                    mk.mk_nama ,
                    mk.mk_color 
                from
                    ms_kriteria mk
                inner join ms_dimensi md on
                    md.md_id = mk.md_id
                where
                    md.md_status = true
                    and mk.mk_status = true
                order by
                    md.md_kode ,
                    mk.mk_kode"
            );

            $data = [];
            $background = [];

            foreach ($kriteria as $k => $v) {
                $data[$k] = [
                    "id" => $v->mk_id,
                    "nama" => $v->mk_nama,
                    "nested" => [
                        "value" => 0
                    ]
                ];
                $background[$k] = $v->mk_color;
            }

            $res["data"] = [
                "data" => $data,
                "backgroundColor" => $background,
            ];
        } catch (\Throwable $th) {
            $res
                = [
                    "status" => false,
                    "msg" => $th->getMessage(),
                ];
        }

        return $res;
    }

    public function getDataTenant(int $user_id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $kriteria = DB::select(
                "SELECT
                    mk.mk_id ,
                    sum(coalesce(ad.mr_bobot,0) * coalesce(msk.msk_bobot,0) / 100) tot_nilai ,
                    sum(coalesce(ad.max_bobot,0) * coalesce(msk.msk_bobot,0) / 100) max_nilai
                from
                    ms_kriteria mk
                inner join ms_dimensi md on
                    md.md_id = mk.md_id
                inner join ms_sub_kriteria msk on
                    msk.mk_id = mk.mk_id
                left join (
                    select
                        sum(coalesce(ad.asd_final, 0) * coalesce(mr.mr_bobot, 0) / 100) as mr_bobot,
                        sskr.msk_id ,
                        sum(coalesce(mr.mr_bobot, 0)) as max_bobot
                    from
                        asesmen_detail ad
                    inner join asesmen a on
                        a.as_id = ad.as_id
                    inner join tenant t on
                        t.tenant_id = a.tenant_id
                        and t.user_id = $user_id
                    inner join setting_sub_kriteria_radar sskr on
                        sskr.sskr_id = ad.sskr_id
                    inner join ms_radar mr on
                        mr.mr_id = sskr.mr_id
                        and mr.mr_status = true
                    group by
                        sskr.msk_id ) ad on
                    ad.msk_id = msk.msk_id
                where
                    mk.mk_status = true
                group by
                    mk.mk_id "
            );

            $data = [];

            foreach ($kriteria as $k => $v) {
                $data[$v->mk_id] = $v->tot_nilai * 100 / $v->max_nilai;
            }

            $res["data"] = $data;
        } catch (\Throwable $th) {
            $res
                = [
                    "status" => false,
                    "msg" => $th->getMessage(),
                ];
        }

        return $res;
    }

    public function cekAsesmen(int $user_id): array
    {

        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $sqlCekAsesmen = "SELECT
                                    a.as_id
                                from
                                    asesmen a
                                inner join tenant t on
                                    t.tenant_id = a.tenant_id
                                where
                                    t.user_id = $user_id";
            $rawCekAsesmen = DB::select($sqlCekAsesmen);
            if (count($rawCekAsesmen) <= 0) {
                $res["data"] = [
                    "has_asesmen" => false,
                ];

                return $res;
            }

            $as_id = $rawCekAsesmen[0]->as_id;
            $sqlPersenAsesmen = "SELECT
                                    count(sskr2.sskr_id) total_task,
                                    count(c.sskr_id) total_data
                                from
                                    setting_sub_kriteria_radar sskr2
                                inner join ms_radar mr on
                                    mr.mr_id = sskr2.mr_id
                                    and mr.mr_status = true
                                inner join ms_sub_kriteria msk on
                                    msk.msk_id = sskr2.msk_id 
                                    and msk.msk_status = true
                                left join (
                                    select
                                        sskr.sskr_id
                                    from
                                        asesmen_detail ad
                                    inner join setting_sub_kriteria_radar sskr on
                                        sskr.sskr_id = ad.sskr_id
                                    inner join asesmen a on
                                        a.as_id = ad.as_id
                                        and a.as_id = $as_id
                                ) c on
                                    c.sskr_id = sskr2.sskr_id ";
            $rawPersenAsesmen = DB::select($sqlPersenAsesmen);
            $res["data"] = [
                "has_asesmen" => true,
                "persen_asesmen" => number_format(floatval($rawPersenAsesmen[0]->total_data) * 100 / floatval($rawPersenAsesmen[0]->total_task), 0),
            ];
        } catch (\Throwable $th) {
            $res
                = [
                    "status" => false,
                    "msg" => $th->getMessage(),
                ];
        }

        return $res;
    }
}
