<?php

namespace App\Providers\My;

use App\Services\Impl\LaporanPenilaianUserServiceImpl;
use App\Services\LaporanPenilaianUserService;
use Illuminate\Support\ServiceProvider;

class LaporanPenilaianUserServiceProvider extends ServiceProvider
{
    public $singletons = [
        LaporanPenilaianUserService::class => LaporanPenilaianUserServiceImpl::class,
    ];

    public function provides(): array
    {
        return [LaporanPenilaianUserService::class];
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
