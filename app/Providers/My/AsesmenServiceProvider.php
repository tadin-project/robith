<?php

namespace App\Providers\My;

use App\Services\AsesmenService;
use App\Services\Impl\AsesmenServiceImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AsesmenServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        AsesmenService::class => AsesmenServiceImpl::class,
    ];

    public function provides(): array
    {
        return [
            AsesmenService::class,
        ];
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
