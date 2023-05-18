<?php

namespace App\Services\Impl;

use App\Models\Asesmen;
use App\Models\AsesmenDetail;
use App\Models\ConvertionValue;
use App\Models\Tenant;
use App\Services\AsesmenService;
use Illuminate\Support\Facades\DB;
use Mavinoo\Batch\BatchFacade;

/**
 * Summary of AsesmenServiceImpl
 */
class AsesmenServiceImpl implements AsesmenService
{
    /**
     * Summary of getKriteria
     * @return array
     */
    public function getKriteria(): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            // $mk = MsKriteria::where("mk_status", true)->orderBy("mk_kode", "asc")->get();
            $sql = "SELECT
                        mk.*
                    from
                        ms_kriteria mk
                    inner join ms_dimensi md on
                        md.md_id = mk.md_id
                    where
                        md.md_status = true
                        and mk.mk_status = true
                    order by
                        md.md_kode,
                        mk.mk_kode ";
            $mk = DB::select($sql);
            $res["data"] = [];
            if (count($mk) > 0) {
                $dtMk = [];
                foreach ($mk as $k => $v) {
                    $dtMk[$k] = [
                        "mk_id" => $v->mk_id,
                        "mk_nama" => $v->mk_nama,
                        "mk_desc" => $v->mk_desc,
                    ];
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

    /**
     * Summary of getSubKriteria
     * @return array
     */
    public function getSubKriteria(): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $sql = "SELECT
                        msk.*
                    from
                        ms_sub_kriteria msk
                    inner join ms_kriteria mk on
                        mk.mk_id = msk.mk_id
                    inner join ms_dimensi md on
                        md.md_id = mk.md_id
                    where
                        msk.msk_status = true
                        and mk.mk_status = true
                        and md.md_status = true
                    order by
                        md.md_kode ,
                        mk.mk_kode ,
                        msk.msk_kode";
            $msk = DB::select($sql);
            $res["data"] = [];
            if (count($msk) > 0) {
                $dtMsk = [];
                foreach ($msk as $k => $v) {
                    $dtMsk[$v->mk_id][] = [
                        "msk_id" => $v->msk_id,
                        "msk_nama" => $v->msk_nama,
                        "msk_is_submission" => $v->msk_is_submission,
                        "mk_id" => $v->mk_id,
                    ];
                }
                $res["data"] = $dtMsk;
            }
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }

    /**
     * Summary of getConvertionValue
     * @return array
     */
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

    public function getTenantByUser(string $id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $tenant = Tenant::where("user_id", $id)->first();
            if (!isset($tenant->tenant_id)) {
                $res["data"] = 0;
                return $res;
            }

            $res["data"] = $tenant->tenant_id;
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }

    public function getById(string $tenant_id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $asesmen = Asesmen::where("tenant_id", $tenant_id)->first();
            if (!isset($asesmen->as_id)) {
                $res = [
                    "status" => true,
                    "data" => 0,
                    "msg" => "",
                ];
                return $res;
            }

            $res["data"] = [
                "as_status" => $asesmen->as_status,
                "detail" => [],
            ];

            $asesmenDetail = AsesmenDetail::where("as_id", $asesmen->as_id)->get();
            foreach ($asesmenDetail as $k => $v) {
                $res["data"]["detail"][$k] = [
                    "msk_id" => $v->msk_id,
                    "asd_value" => $v->asd_value,
                    "asd_file" => $v->asd_file,
                    "id_detail" => $v->asd_id,
                ];
            }
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }

    public function cekAsesmen(string $tenant_id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $dt = Asesmen::where("tenant_id", $tenant_id)->first();
            if (!isset($dt->as_id)) {
                $res["data"] = 0;
            } else {
                $res["data"] = $dt->as_id;
            }
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }

    public function add(array $data): array
    {
        DB::beginTransaction();
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $dt = Asesmen::create($data);
            if (!isset($dt->as_id)) {
                $res = [
                    "status" => false,
                    "msg" => "Gagal menambahkan data. Silahkan hubungi Admin!",
                ];
                return $res;
            }

            $res["data"] = $dt->as_id;
            $res["detail"] = $dt;
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        DB::commit();

        return $res;
    }

    public function edit(string $id, array $data): array
    {
        DB::beginTransaction();
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $dt = Asesmen::find($id);
            if (!isset($dt->as_id)) {
                $res = [
                    "status" => false,
                    "msg" => "Data tidak ditemukan. Silahkan hubungi Admin!",
                ];
                return $res;
            }

            $r = $dt->update($data);
            if ($r < 0) {
                $res = [
                    "status" => false,
                    "msg" => "Gagal update data. Silahkan hubungi Admin!",
                ];
                return $res;
            }

            $res["data"] = $dt->as_id;
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        DB::commit();

        return $res;
    }

    public function addDetail(array $data): array
    {
        DB::beginTransaction();
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            AsesmenDetail::insert($data);
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        DB::commit();

        return $res;
    }

    public function editDetail(array $data): array
    {
        DB::beginTransaction();
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $asesmenDetailInstance = new AsesmenDetail();

            BatchFacade::update($asesmenDetailInstance, $data, "asd_id");
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }
        DB::commit();

        return $res;
    }

    public function getDetailById(string $asd_id): array
    {
        try {
            $dtDetail = AsesmenDetail::find($asd_id);
            if (!isset($dtDetail->asd_id)) {
                $res = [];
            } else {
                $res = [
                    "asd_file" => $dtDetail->asd_file,
                ];
            }
        } catch (\Throwable $th) {
            $res = [];
        }

        return $res;
    }

    public function hapusLampiran(string $dir, array $list_id_asd): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            // ambil semua file yang akan dihapus
            $dt = AsesmenDetail::whereIn("asd_id", $list_id_asd);
            foreach ($dt->get() as $k => $v) {
                if (!empty($v->asd_file)) {
                    $this->hapusFile("./" . $dir . "/" . $v->asd_file);
                }
            }

            // update asd_file = null
            $dt->update(["asd_file" => null]);
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }

    public function hapusFile(string $file): void
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public function cekHasComplete(string $tenant_id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $sql = "SELECT
                        sum(1) as tot,
                        sum(ad2.tot) as tot_fill
                    from
                        ms_sub_kriteria msk
                    left join (
                        select
                            1 as tot,
                            ad.msk_id
                        from
                            asesmen a
                        inner join asesmen_detail ad on
                            ad.as_id = a.as_id
                        where
                            a.tenant_id = $tenant_id) ad2 on
                        ad2.msk_id = msk.msk_id";
            $dt = DB::select($sql);
            if ($dt[0]->tot == $dt[0]->tot_fill) {
                $res["data"] = true;
            } else {
                $res["data"] = false;
            }
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }

    public function editNonSubmission(string $as_id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $sql = "SELECT
                        ad.asd_id 
                    from
                        asesmen_detail ad
                    inner join ms_sub_kriteria msk on
                        msk.msk_id = ad.msk_id
                    where
                        ad.as_id = $as_id
                        and msk.msk_is_submission = 0;";
            $rawDataAsesmenDetail = DB::select($sql);
            if (count($rawDataAsesmenDetail) <= 0) {
                return $res;
            }

            $dataAsesmenDetail = [];
            foreach ($rawDataAsesmenDetail as $v) {
                $dataAsesmenDetail[] = $v->asd_id;
            }

            DB::table("asesmen_detail")->whereIn("asd_id", $dataAsesmenDetail)->update([
                "asd_status" => 1,
            ]);
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }
}
