<?php

namespace App\Providers\My;

use App\Services\Impl\TenantServiceImpl;
use App\Services\TenantService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        TenantService::class => TenantServiceImpl::class,
    ];

    public function provides(): array
    {
        return [TenantService::class];
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
