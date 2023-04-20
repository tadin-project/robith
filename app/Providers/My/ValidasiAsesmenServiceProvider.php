<?php

namespace App\Providers\My;

use App\Services\Impl\ValidasiAsesmenServiceImpl;
use App\Services\ValidasiAsesmenService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ValidasiAsesmenServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        ValidasiAsesmenService::class => ValidasiAsesmenServiceImpl::class,
    ];

    public function provides(): array
    {
        return [
            ValidasiAsesmenService::class,
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
