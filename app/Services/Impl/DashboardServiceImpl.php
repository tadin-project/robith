<?php

namespace App\Services\Impl;

use App\Models\MsKriteria;
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
            dd($th->getMessage());
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
                    sum(coalesce(ad.asd_value,0) * msk.msk_bobot / 100) tot_nilai
                from
                    ms_kriteria mk
                inner join ms_dimensi md on
                    md.md_id = mk.md_id
                inner join ms_sub_kriteria msk on
                    msk.mk_id = mk.mk_id
                left join (
                    select
                        ad2.msk_id,
                        case 
                            when a.as_status = 2 then
                                case
                                    when ad2.asd_status = 2 then 0
                                    else ad2.asd_value
                                end 
                            else 0 
                        end as asd_value
                    from
                        asesmen a
                    inner join asesmen_detail ad2 on
                        ad2.as_id = a.as_id
                    where
                        a.user_id = $user_id) ad on
                    ad.msk_id = msk.msk_id
                where
                    mk.mk_status = true
                group by
                    mk.mk_id "
            );

            $data = [];

            foreach ($kriteria as $k => $v) {
                $data[$v->mk_id] = $v->tot_nilai;
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
}
