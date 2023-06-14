<?php

namespace App\Services\Impl;

use App\Services\LaporanPenilaianService;
use Illuminate\Support\Facades\DB;

class LaporanPenilaianServiceImpl implements LaporanPenilaianService
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
                            count(a.as_id) total
                        from
                            asesmen a
                        inner join ms_users v on
                            v.user_id = a.valid_by
                        inner join tenant t on
                            t.tenant_id = a.tenant_id 
                        inner join ms_users mu on
                            mu.user_id = t.user_id 
                        where
                            a.as_status = 2 $where";
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
                    "a.as_id",
                    "a.updated_at",
                    "coalesce(mu.user_fullname, mu.user_name) as user_tenant",
                    "coalesce(v.user_fullname, v.user_name) as validator",
                    "a.as_total",
                    "a.as_max",
                    "t.tenant_nama",
                ];
            }

            $slc = implode(',', $cols);
            $qdata = "SELECT
                            $slc
                        from
                            asesmen a
                        inner join ms_users v on
                            v.user_id = a.valid_by
                        inner join tenant t on
                            t.tenant_id = a.tenant_id 
                        inner join ms_users mu on
                            mu.user_id = t.user_id 
                        where
                            a.as_status = 2 $where
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

            $tenant = DB::select(
                "SELECT
                    concat(coalesce(mu.user_fullname, mu.user_name), ' (', t.tenant_nama , ')') as tenant_nama
                from
                    asesmen a
                inner join tenant t on
                    t.tenant_id = a.tenant_id
                inner join ms_users mu on
                    mu.user_id = t.user_id
                where
                    a.as_id = $id"
            )[0]->tenant_nama;
            $res["data"]["tenant_nama"] = $tenant;

            $sqlKriteria = "SELECT
                                mk.*
                            from
                                ms_kriteria mk
                            inner join ms_dimensi md on
                                md.md_id = mk.md_id
                            order by
                                md.md_kode ,
                                mk.mk_kode";
            $rawKriteria = DB::select($sqlKriteria);
            $tr = "";
            $rata2 = 0;
            $total = 0;
            $totIndexSubKriteria = 0;
            if (count($rawKriteria) > 0) {
                foreach ($rawKriteria as $k => $v) {
                    $rata2 = 0;
                    $total = 0;
                    $totIndexSubKriteria = 0;
                    $totBobot = 0;
                    $tr .= "<tr>
                        <th>$v->mk_nama</th>
                        ";

                    $sqlSubKriteria = "SELECT
                                            msk.msk_id ,
                                            msk.msk_nama ,
                                            msk.msk_bobot 
                                        from
                                            ms_sub_kriteria msk
                                        where
                                            msk.mk_id = $v->mk_id
                                            and msk.msk_status = true
                                        order by
                                            msk.msk_kode";
                    $rawSubKriteria = DB::select($sqlSubKriteria);
                    $trSubKriteria = "";
                    if (count($rawSubKriteria) > 0) {
                        foreach ($rawSubKriteria as $k2 => $v2) {
                            $sqlRadar = "SELECT
                                            mr.mr_nama ,
                                            mr.mr_bobot ,
                                            ad.asd_final
                                        from
                                            asesmen_detail ad
                                        inner join setting_sub_kriteria_radar sskr on
                                            sskr.sskr_id = ad.sskr_id
                                        inner join ms_radar mr on
                                            mr.mr_id = sskr.mr_id
                                        where
                                            ad.as_id = $id
                                            and sskr.msk_id = $v2->msk_id
                                            and mr.mr_status = true
                                        order by
                                            mr.mr_kode ;";
                            $rawRadar = DB::select($sqlRadar);
                            $subNilaiRadar = 0;
                            $trRadar = "";
                            if (count($rawRadar) > 0) {
                                foreach ($rawRadar as $k3 => $v3) {
                                    $nilaiRadar = floatval($v3->mr_bobot) * floatval($v3->asd_final) / 100;
                                    $subNilaiRadar += $nilaiRadar;
                                    $trRadar .= "<tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;$v3->mr_nama</td>
                                        <td class='text-right'>$nilaiRadar%</td>
                                    </tr>";
                                }
                            }
                            // $bobot = $v2->asd_status == 2 ? $nilaiMin : $v2->asd_value;
                            // $subBobot = $bobot * $v2->msk_bobot / 100;
                            $nilaiSubKriteria = $subNilaiRadar * floatval($v2->msk_bobot) / 100;
                            $trSubKriteria .= "<tr>
                                <th>&nbsp;&nbsp;$v2->msk_nama</th>
                                <th class='text-right'>$nilaiSubKriteria</th>
                            </tr>" . $trRadar;

                            $total += floatval($v2->msk_bobot);
                            $totBobot += floatval($nilaiSubKriteria);
                            $totIndexSubKriteria++;
                        }
                    }

                    $rata2 = $totIndexSubKriteria == 0 ? 0 : $totBobot / $totIndexSubKriteria;

                    $tr .= "<th class='text-right' width='30%'>Total Nilai : $totBobot<br>Rata - rata : $rata2</th>
                    </tr>";
                    $tr .= $trSubKriteria;
                }
            }

            $res["data"]["detail"] = $tr;
        } catch (\Throwable $th) {
            $res = [
                'status' => false,
                'msg' => $th->getMessage(),
                "stack" => env("DEV_MODE", "production") == "production" ? null : $th->getTrace(),
            ];
        }

        return $res;
    }
}
