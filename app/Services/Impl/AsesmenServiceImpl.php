<?php

namespace App\Services\Impl;

use App\Models\Asesmen;
use App\Models\AsesmenDetail;
use App\Models\MsKriteria;
use App\Models\MsSubKriteria;
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
}
