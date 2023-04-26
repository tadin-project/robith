<?php

namespace App\Providers\My;

use App\Services\Impl\MsLampiranServiceImpl;
use App\Services\MsLampiranService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MsLampiranServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        MsLampiranService::class => MsLampiranServiceImpl::class
    ];

    public function provides(): array
    {
        return [MsLampiranService::class];
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
