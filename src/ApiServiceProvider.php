<?php

namespace DavidNineRoc\ApiHelper;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // 生成控制器
        $this->publishes([
            __DIR__ . '/Controllers/Api/ApiController.tpl' => app_path('Http//Controllers/Api/ApiController.php'),
            __DIR__ . '/Services/ResponseServe.tpl' => app_path('Http/Services/ResponseServe.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
