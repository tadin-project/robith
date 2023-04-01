<?php

namespace App\Providers\My;

use App\Services\Impl\MsMenusServiceImpl;
use App\Services\MsMenusService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MsMenusServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        MsMenusService::class => MsMenusServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsMenusService::class];
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
