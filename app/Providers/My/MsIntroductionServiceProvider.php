<?php

namespace App\Providers\My;

use App\Services\Impl\MsIntroductionServiceImpl;
use App\Services\MsIntroductionService;
use Illuminate\Support\ServiceProvider;

class MsIntroductionServiceProvider extends ServiceProvider
{
    public $singletons = [
        MsIntroductionService::class => MsIntroductionServiceImpl::class,
    ];

    public function provides(): array
    {
        return [MsIntroductionService::class];
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
