<?php

namespace DavidNineRoc\ApiHelper;

use DavidNineRoc\ApiHelper\Commands\MakeApiController;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // 发布配置文件
        $this->publishes([
            __DIR__.'/Config/apihelper.php' => config_path('apihelper.php'),
        ], 'config');

        // 发布基础控制器
        $this->publishes([
            __DIR__.'/Controllers/ApiController.tpl' => app_path('Http//Controllers/Api/ApiController.php'),
            __DIR__.'/Services/ResponseServe.tpl' => app_path('Http/Services/ResponseServe.php'),
        ]);

        // 绑定需要注册的命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeApiController::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
