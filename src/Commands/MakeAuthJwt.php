<?php

namespace DavidNineRoc\ApiHelper\Commands;

use Illuminate\Filesystem\Filesystem;

class MakeAuthJwt extends BaseMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:apiAuth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建 jwt 方式登录验证';

    /**
     * 文件操作实例
     * @var Filesystem
     */
    protected $files;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 发布配置
//        $this->call('vendor:publish', [ '--provider' => 'Tymon\JWTAuth\Providers\LaravelServiceProvider']);
//        // 生成密钥
//        $this->call('jwt:secret');
//        // 合并 config/auth.php 配置
//        $this->mergeAuthConfig(
//            config_path('auth.php')
//        );

        // 写入路由

        // 发布控制器
        $this->publishAuthController();
    }

    /**
     * 把 jwt-auth 所需的配置合并到 config/auth.php 中
     * @param $path
     */
    public function mergeAuthConfig($path)
    {
        $config = $this->files->get($path);

        $search  = [
            "'guard' => 'web'",
            "'driver' => 'token'"
        ];
        $replace = [
            "'guard' => 'api'",
            "'driver' => 'jwt'"
        ];
        $config = str_replace($search, $replace, $config, $count);

        if (
            $count > 0 &&
            $this->files->put($path, $config) == strlen($config)
        ) {
            $this->info('auth configure success');
        } else {
            $this->info('please configure manually config/auth.php');
        }
    }

    protected function publishAuthController()
    {
        // 创建基类
        $this->createBase();

        $this->createFromName(
            $this->getDefaultNamespace('').'/AuthController',
            __DIR__.'/../Auth/AuthController.tpl',
            'AuthController'
        );
    }
}
