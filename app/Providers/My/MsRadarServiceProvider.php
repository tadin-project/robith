<?php

namespace App\Providers\My;

use App\Services\Impl\MsRadarServiceImpl;
use App\Services\MsRadarService;
use Illuminate\Support\ServiceProvider;

class MsRadarServiceProvider extends ServiceProvider
{
    public $singletons = [
        MsRadarService::class => MsRadarServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsRadarService::class];
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
