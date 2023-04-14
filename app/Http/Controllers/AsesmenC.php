<?php

namespace App\Http\Controllers;

use App\Services\AsesmenService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsesmenC extends MyC
{
    private AsesmenService $asesmenService;
    public function __construct(AsesmenService $asesmenService)
    {
        parent::__construct();
        $this->middleware("has_akses:asesmen");
        $this->asesmenService = $asesmenService;
    }

    public function index(): View
    {
        $data = [
            "__title" => "Asesmen",
        ];

        return $this->my_view("v_asesmen", $data);
    }
}
