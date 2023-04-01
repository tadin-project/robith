<?php

namespace App\Providers\My;

use App\Services\Impl\MsKategoriServiceImpl;
use App\Services\MsKategoriService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MsKategoriServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        MsKategoriService::class => MsKategoriServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsKategoriService::class];
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
