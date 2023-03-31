<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardC extends MyC
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware("has_akses:dashboard");
    }

    public function index()
    {
        return $this->my_view("v_dashboard");
    }
}
