<?php

namespace App\Providers\My;

use App\Services\Impl\SettingSubKriteriaRadarServiceImpl;
use App\Services\SettingSubKriteriaRadarService;
use Illuminate\Support\ServiceProvider;

class SettingSubKriteriaRadarServiceProvider extends ServiceProvider
{
    public $singletons = [
        SettingSubKriteriaRadarService::class => SettingSubKriteriaRadarServiceImpl::class,
    ];

    public function provides(): array
    {
        return [SettingSubKriteriaRadarService::class];
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
