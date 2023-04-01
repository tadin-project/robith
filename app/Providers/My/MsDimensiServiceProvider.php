<?php

namespace App\Providers\My;

use App\Services\Impl\MsDimensiServiceImpl;
use App\Services\MsDimensiService;
use Illuminate\Support\ServiceProvider;

class MsDimensiServiceProvider extends ServiceProvider
{
    public $singletons = [
        MsDimensiService::class => MsDimensiServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsDimensiService::class];
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
