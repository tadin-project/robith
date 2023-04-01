<?php

namespace App\Providers\My;

use App\Services\Impl\MsKriteriaServiceImpl;
use App\Services\MsKriteriaService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MsKriteriaServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        MsKriteriaService::class => MsKriteriaServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsKriteriaService::class];
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
