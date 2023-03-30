<?php

namespace App\Providers\My;

use App\Services\Impl\MsGroupsServiceImpl;
use App\Services\MsGroupsService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MsGroupsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        MsGroupsService::class => MsGroupsServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsGroupsService::class];
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
