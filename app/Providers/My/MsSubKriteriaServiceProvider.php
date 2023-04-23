<?php

namespace App\Providers\My;

use App\Services\Impl\MsSubKriteriaServiceImpl;
use App\Services\MsSubKriteriaService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MsSubKriteriaServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public $singletons = [
        MsSubKriteriaService::class => MsSubKriteriaServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsSubKriteriaService::class];
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
