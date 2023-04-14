<?php

namespace App\Services\Impl;

use App\Models\MsKriteria;
use App\Models\MsSubKriteria;
use App\Services\AsesmenService;

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
            if ($mk->count() > 0) {
                $res["data"] = $mk;
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
     * @param string $mk_id
     * @return array
     */
    public function getSubKriteria(string $mk_id): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            if (empty($mk_id)) {
                $res = [
                    "status" => false,
                    "msg" => "Id kategori diperlukan!",
                ];
                return $res;
            }

            $msk = MsSubKriteria::where("msk_status", true)->where("mk_id", $mk_id)->orderBy("msk_kode", "asc")->get();
            if ($msk->count() > 0) {
                $res["data"] = $msk;
            }
        } catch (\Throwable $th) {
            $res = [
                "status" => false,
                "msg" => $th->getMessage(),
            ];
        }

        return $res;
    }
}
