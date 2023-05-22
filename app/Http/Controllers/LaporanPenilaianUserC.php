<?php

namespace App\Http\Controllers;

use App\Services\LaporanPenilaianUserService;
use Illuminate\Contracts\View\View;
use PDF;

class LaporanPenilaianUserC extends MyC
{
    private LaporanPenilaianUserService $LaporanPenilaianUserService;
    public function __construct(LaporanPenilaianUserService $LaporanPenilaianUserService)
    {
        parent::__construct();
        $this->middleware("has_akses:laporan-penilaian-user");
        $this->LaporanPenilaianUserService = $LaporanPenilaianUserService;
    }

    public function index(): View
    {
        $kriteria = [];
        $cekKriteriaUser = $this->LaporanPenilaianUserService->getKriteriaData($this->__sess_user["user_id"]);
        if ($cekKriteriaUser["status"]) {
            $kriteria = $cekKriteriaUser["data"];
        }

        $data = [
            "__title" => "Laporan Penilaian",
            "kriteria" => $kriteria,
        ];

        return $this->my_view("v_laporan_penilaian_user", $data);
    }

    public function cetak()
    {
        $kriteria = [];
        $cekKriteriaUser = $this->LaporanPenilaianUserService->cetak($this->__sess_user["user_id"]);
        if ($cekKriteriaUser["status"]) {
            $kriteria = $cekKriteriaUser["data"];
        }

        // echo view('cetak/v_laporan_penilaian_user_cetak', ["kriteria" => $kriteria]);
        $pdf = PDF::loadview('cetak/v_laporan_penilaian_user_cetak', ["kriteria" => $kriteria]);
        return $pdf->stream('laporan-penilaian.pdf');
    }
}
