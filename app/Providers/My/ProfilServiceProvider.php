<?php

namespace App\Providers\My;

use App\Services\Impl\ProfilServiceImpl;
use App\Services\ProfilService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ProfilServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        ProfilService::class => ProfilServiceImpl::class,
    ];
    public function provides(): array
    {
        return [ProfilService::class];
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
