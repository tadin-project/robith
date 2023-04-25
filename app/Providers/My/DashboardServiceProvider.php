<?php

namespace App\Providers\My;

use App\Services\DashboardService;
use App\Services\Impl\DashboardServiceImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        DashboardService::class => DashboardServiceImpl::class,
    ];

    public function provides(): array
    {
        return [DashboardService::class];
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
