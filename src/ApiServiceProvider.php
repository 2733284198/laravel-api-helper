<?php

namespace DavidNineRoc\ApiHelper;

use DavidNineRoc\ApiHelper\Commands\MakeAuthJwt;
use DavidNineRoc\ApiHelper\Commands\MakeApiController;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * 引导应用程序服务。
     */
    public function boot()
    {
        // 发布配置文件
        $this->publishes(
            [
                __DIR__.'/Config/apihelper.php' => config_path('apihelper.php'),
            ],
            'config'
        );



        if ($this->app->runningInConsole()) {
            $this->commands([
                // 创建 API 控制器
                MakeApiController::class,
                // 创建 jwt 验证
                MakeAuthJwt::class,
            ]);
        }
    }

    /**
     * 注册应用程序服务。
     */
    public function register()
    {

    }
}
