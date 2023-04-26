<?php

namespace App\Http\Controllers;

use App\Services\LampiranService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LampiranC extends MyC
{
    private LampiranService $lampiranService;
    private $dirUploads = "uploads/lampiran";
    public function __construct(LampiranService $lampiranService)
    {
        parent::__construct();
        $this->middleware("has_akses:lampiran");
        $this->lampiranService = $lampiranService;
    }

    public function index(): View
    {
        $listLampiran = [];
        $cekListLampiran = $this->lampiranService->getData();
        if ($cekListLampiran["status"]) {
            $listLampiran = $cekListLampiran["data"];
        }

        $data = [
            "__title" => "Lampiran",
            "data" => $listLampiran,
            "dir_uploads" => $this->dirUploads,
        ];

        return $this->my_view("v_lampiran", $data);
    }
}
