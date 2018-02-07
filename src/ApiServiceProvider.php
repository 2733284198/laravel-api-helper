<?php

namespace DavidNineRoc\ApiHelper;

use DavidNineRoc\ApiHelper\Commands\MakeApiController;
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
            __DIR__ . '/Controllers/ApiController.tpl' => app_path('Http//Controllers/Api/ApiController.php'),
            __DIR__ . '/Services/ResponseServe.tpl' => app_path('Http/Services/ResponseServe.php')
        ]);

        // 绑定控制器生成命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeApiController::class
            ]);
        }
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
