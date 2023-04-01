<?php

namespace App\Providers\My;

use App\Services\Impl\MsSubKategoriServiceImpl;
use App\Services\MsSubKategoriService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MsSubKategoriServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        MsSubKategoriService::class => MsSubKategoriServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsSubKategoriService::class];
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
