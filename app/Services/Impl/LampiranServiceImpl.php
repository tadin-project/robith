<?php

namespace App\Services\Impl;

use App\Models\MsLampiran;
use App\Services\LampiranService;

class LampiranServiceImpl implements LampiranService
{
    public function getData(): array
    {
        $res = [
            "status" => true,
            "msg" => "",
        ];

        try {
            $data = MsLampiran::where("lampiran_status", true)->orderBy("lampiran_kode", "asc")->get();
            $res["data"] = $data;
        } catch (\Throwable $th) {
            $res["status"] = false;
            $res["msg"] = $th->getMessage();
        }

        return $res;
    }
}
