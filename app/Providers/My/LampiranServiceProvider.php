<?php

namespace App\Providers\My;

use App\Services\Impl\LampiranServiceImpl;
use App\Services\LampiranService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class LampiranServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        LampiranService::class => LampiranServiceImpl::class,
    ];

    public function provides(): array
    {
        return [LampiranService::class];
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
