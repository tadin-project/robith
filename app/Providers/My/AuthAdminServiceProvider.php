<?php

namespace App\Providers\My;

use App\Services\AuthAdminService;
use App\Services\Impl\AuthAdminServiceImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AuthAdminServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        AuthAdminService::class => AuthAdminServiceImpl::class,
    ];

    public function provides(): array
    {
        return [AuthAdminService::class];
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
