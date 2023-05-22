<?php

namespace App\Providers\My;

use App\Services\Impl\LaporanPenilaianServiceImpl;
use App\Services\LaporanPenilaianService;
use Illuminate\Support\ServiceProvider;

class LaporanPenilaianServiceProvider extends ServiceProvider
{
    public $singletons = [
        LaporanPenilaianService::class => LaporanPenilaianServiceImpl::class,
    ];

    public function provides(): array
    {
        return [LaporanPenilaianService::class];
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
