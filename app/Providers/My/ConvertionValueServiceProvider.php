<?php

namespace App\Providers\My;

use App\Services\Impl\ConvertionValueServiceImpl;
use App\Services\ConvertionValueService;
use Illuminate\Support\ServiceProvider;

class ConvertionValueServiceProvider extends ServiceProvider
{
    public $singletons = [
        ConvertionValueService::class => ConvertionValueServiceImpl::class,
    ];

    public function provides(): array
    {
        return [ConvertionValueService::class];
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
