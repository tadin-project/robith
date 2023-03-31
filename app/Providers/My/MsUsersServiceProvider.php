<?php

namespace App\Providers\My;

use App\Services\Impl\MsUsersServiceImpl;
use App\Services\MsUsersService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MsUsersServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        MsUsersService::class => MsUsersServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsUsersService::class];
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
