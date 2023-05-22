<?php

namespace App\Services\Impl;

use App\Models\ConvertionValue;
use App\Services\LaporanPenilaianService;
use Illuminate\Support\Facades\DB;
use stdClass;

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

            $nilaiMin = 0;
            $dataNilai = ConvertionValue::where("cval_status", true)->orderBy("cval_kode", "asc")->get();
            if ($dataNilai->count() > 0) {
                $nilaiMin = floatval($dataNilai[0]->cval_nilai);
            }

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
            $dataSubKriteria = null;
            $tr = "";
            $subTr = "";
            $rata2 = 0;
            $total = 0;
            $totIndexSubKriteria = 0;
            $nilai = 0;
            if (count($rawKriteria) > 0) {
                foreach ($rawKriteria as $k => $v) {

                    $rata2 = 0;
                    $total = 0;
                    $totIndexSubKriteria = 0;
                    $bobot = 0;
                    $nilai = 0;
                    $tr .= "<tr>
                        <th>$v->mk_nama</th>
                        ";

                    $sqlSubKriteria = "SELECT
                                            msk.msk_nama ,
                                            msk.msk_bobot ,
                                            ad.asd_value ,
                                            ad.asd_status 
                                        from
                                            ms_sub_kriteria msk
                                        left join asesmen_detail ad on
                                            ad.msk_id = msk.msk_id
                                            and ad.as_id = $id
                                        where
                                            msk.mk_id = $v->mk_id
                                            and msk.msk_status = true
                                        order by
                                            msk.msk_kode";
                    $rawSubKriteria = DB::select($sqlSubKriteria);
                    $subTr = "";
                    if (count($rawSubKriteria) > 0) {
                        foreach ($rawSubKriteria as $k2 => $v2) {
                            $subNilai = $v2->asd_status == 2 ? $nilaiMin : $v2->asd_value;
                            $subBobot = $subNilai * $v2->msk_bobot / 100;
                            $subTr .= "<tr>
                                <td>$v2->msk_nama</td>
                                <td class='text-right'>$subBobot</td>
                            </tr>";

                            $total += floatval($v2->msk_bobot);
                            $bobot += floatval($subBobot);
                            $totIndexSubKriteria++;
                        }
                    }

                    $rata2 = $totIndexSubKriteria == 0 ? 0 : $bobot / $totIndexSubKriteria;

                    $tr .= "<th class='text-right' width='30%'>Total Nilai : $bobot<br>Rata - rata : $rata2</th>
                    </tr>";
                    $tr .= $subTr;
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
