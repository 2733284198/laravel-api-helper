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


        // 绑定需要注册的命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                // 创建 API 控制器
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
