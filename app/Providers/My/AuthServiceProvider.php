<?php

namespace App\Providers\My;

use App\Services\AuthService;
use App\Services\Impl\AuthServiceImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        AuthService::class => AuthServiceImpl::class,
    ];

    public function provides(): array
    {
        return [AuthService::class];
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
