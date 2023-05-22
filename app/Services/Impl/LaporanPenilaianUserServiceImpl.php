<?php

namespace App\Services\Impl;

use App\Models\ConvertionValue;
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

            $nilaiMin = 0;
            $dataNilai = ConvertionValue::where("cval_status", true)->orderBy("cval_kode", "asc")->get();
            if ($dataNilai->count() > 0) {
                $nilaiMin = floatval($dataNilai[0]->cval_nilai);
            }

            $sqlKriteria = "SELECT
                                msk.msk_id ,
                                mk.mk_id ,
                                mk.mk_nama ,
                                ad.asd_status ,
                                ad.asd_value ,
                                msk.msk_bobot 
                            from
                                tenant t
                            inner join asesmen a on
                                a.tenant_id = t.tenant_id
                                and t.user_id = $user_id
                                and a.as_status = 2
                            inner join asesmen_detail ad on
                                ad.as_id = a.as_id
                            inner join ms_sub_kriteria msk on
                                msk.msk_id = ad.msk_id
                            inner join ms_kriteria mk on
                                mk.mk_id = msk.mk_id
                                and mk.mk_status = true
                            inner join ms_dimensi md on
                                md.md_id = mk.md_id
                            order by
                                md.md_kode ,
                                mk.mk_kode ,
                                msk.msk_id";
            $rawKriteria = DB::select($sqlKriteria);
            $data = [];
            if (count($rawKriteria) > 0) {
                $mk_id = 0;
                $subRata2 = 0;
                $banyakSubKriteria = 0;
                // $subTotal = 0;
                $subNilai = 0;
                $subBobot = 0;
                foreach ($rawKriteria as $k => $v) {
                    $subNilai = $v->asd_status == 2 ? $nilaiMin : $v->asd_value;

                    if (!array_key_exists($v->mk_id, $data)) {
                        $banyakSubKriteria++;
                        if ($mk_id != 0) {
                            $subRata2 = $banyakSubKriteria == 0 ? 0 : $subBobot / $banyakSubKriteria;
                            $data[$mk_id]["bobot"] = $subBobot;
                            $data[$mk_id]["rata2"] = $subRata2;
                        }

                        $banyakSubKriteria = 0;
                        // $subTotal = 0;
                        $subBobot = 0;
                        $data[$v->mk_id] = [
                            "mk_nama" => $v->mk_nama,
                        ];
                        $subBobot += (floatval($subNilai) * floatval($v->msk_bobot) / 100);
                        // $subTotal += $v->msk_bobot;
                        $mk_id = $v->mk_id;
                    } else {
                        $banyakSubKriteria++;
                        $subBobot += (floatval($subNilai) * floatval($v->msk_bobot) / 100);
                    }

                    if ($k == count($rawKriteria) - 1) {
                        $subRata2 = $banyakSubKriteria == 0 ? 0 : $subBobot / $banyakSubKriteria;
                        $data[$mk_id]["bobot"] = $subBobot;
                        $data[$mk_id]["rata2"] = $subRata2;
                    }
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

            $nilaiMin = 0;
            $dataNilai = ConvertionValue::where("cval_status", true)->orderBy("cval_kode", "asc")->get();
            if ($dataNilai->count() > 0) {
                $nilaiMin = floatval($dataNilai[0]->cval_nilai);
            }

            $sqlKriteria = "SELECT
                                msk.msk_id ,
                                mk.mk_id ,
                                mk.mk_nama ,
                                ad.asd_status ,
                                ad.asd_value ,
                                msk.msk_bobot ,
                                msk.msk_nama 
                            from
                                tenant t
                            inner join asesmen a on
                                a.tenant_id = t.tenant_id
                                and t.user_id = $user_id
                                and a.as_status = 2
                            inner join asesmen_detail ad on
                                ad.as_id = a.as_id
                            inner join ms_sub_kriteria msk on
                                msk.msk_id = ad.msk_id
                            inner join ms_kriteria mk on
                                mk.mk_id = msk.mk_id
                                and mk.mk_status = true
                            inner join ms_dimensi md on
                                md.md_id = mk.md_id
                            order by
                                md.md_kode ,
                                mk.mk_kode ,
                                msk.msk_kode";
            $rawKriteria = DB::select($sqlKriteria);
            $data = [];
            if (count($rawKriteria) > 0) {
                $mk_id = 0;
                $subRata2 = 0;
                $banyakSubKriteria = 0;
                // $subTotal = 0;
                $subNilai = 0;
                $subBobot = 0;
                $subTotBobot = 0;
                foreach ($rawKriteria as $k => $v) {
                    $subNilai = $v->asd_status == 2 ? $nilaiMin : $v->asd_value;

                    if (!array_key_exists($v->mk_id, $data)) {
                        $banyakSubKriteria++;
                        if ($mk_id != 0) {
                            $subRata2 = $banyakSubKriteria == 0 ? 0 : $subTotBobot / $banyakSubKriteria;
                            $data[$mk_id]["bobot"] = $subTotBobot;
                            $data[$mk_id]["rata2"] = $subRata2;
                        }

                        $banyakSubKriteria = 0;
                        // $subTotal = 0;
                        $subTotBobot = 0;
                        $data[$v->mk_id] = [
                            "mk_nama" => $v->mk_nama,
                            "children" => [],
                        ];
                        $subBobot = (floatval($subNilai) * floatval($v->msk_bobot) / 100);
                        $subTotBobot += $subBobot;
                        $data[$v->mk_id]["children"][] = [
                            "msk_nama" => $v->msk_nama,
                            "bobot" => $subBobot,
                        ];
                        // $subTotal += $v->msk_bobot;
                        $mk_id = $v->mk_id;
                    } else {
                        $banyakSubKriteria++;
                        $subBobot = (floatval($subNilai) * floatval($v->msk_bobot) / 100);
                        $subTotBobot += $subBobot;
                        $data[$v->mk_id]["children"][] = [
                            "msk_nama" => $v->msk_nama,
                            "bobot" => $subBobot,
                        ];
                    }

                    if ($k == count($rawKriteria) - 1) {
                        $subRata2 = $banyakSubKriteria == 0 ? 0 : $subTotBobot / $banyakSubKriteria;
                        $data[$mk_id]["bobot"] = $subTotBobot;
                        $data[$mk_id]["rata2"] = $subRata2;
                    }
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
}
