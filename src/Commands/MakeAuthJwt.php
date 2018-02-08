<?php

namespace DavidNineRoc\ApiHelper\Commands;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Tymon\JWTAuth\Contracts\JWTSubject;

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

        // 修改 User 模型
        $this->updateUserModel();

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

    /**
     * 发布有关验证的控制器
     */
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

    protected function updateUserModel()
    {
        // 先获取到 模型，
        $model = config('auth.providers.users.model', '\App\User');

        if (! class_exists($model)) {
            throw new ModelNotFoundException('User 模型不存在，请配置 auth.providers.users.model 参数');
        }

        // 如果还没有实现 JWTSubject 接口
        if (! app()->make($model) instanceof JWTSubject) {
            $this->implementInterface($model);
        }

        $this->info('User implements JWTSubject');
        dd();

    }

    protected function implementInterface($model)
    {
        // 根据命名空间得到文件路径
        $path = $this->getPath($model);

        $namespace = <<<namespace
namespace App\Http;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable  implements JWTSubject
namespace;


        $methods = <<<method
        
    /**
     * 获取将存储在JWT主题声明中的标识符。
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return \$this->getKey();
    }

    /**
     * 返回一个键值数组，其中包含要添加到JWT的任何自定义声明。
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
method;

        $content = $this->files->get($path);
        // 替换命名空间部分
        $content = preg_replace(
            '/namespace[\s\S]+class User extends Authenticatable/',
            $namespace,
            $content
        );
        // 增加实现方法
        $content = preg_replace(
            '/}[\s]*$/',
            $methods,
            $content
        );

        $this->files->put($path, $content);
    }
}
