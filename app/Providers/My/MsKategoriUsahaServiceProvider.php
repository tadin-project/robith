<?php

namespace App\Providers\My;

use App\Services\Impl\MsKategoriUsahaServiceImpl;
use App\Services\MsKategoriUsahaService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MsKategoriUsahaServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        MsKategoriUsahaService::class => MsKategoriUsahaServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsKategoriUsahaService::class];
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
