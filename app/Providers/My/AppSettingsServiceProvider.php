<?php

namespace App\Providers\My;

use App\Services\AppSettingsService;
use App\Services\Impl\AppSettingsServiceImpl;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AppSettingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $singletons = [
        AppSettingsService::class => AppSettingsServiceImpl::class,
    ];

    public function provides(): array
    {
        return [
            AppSettingsService::class,
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
