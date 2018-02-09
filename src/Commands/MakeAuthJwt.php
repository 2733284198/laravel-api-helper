<?php

namespace DavidNineRoc\ApiHelper\Commands;

use App\Providers\RouteServiceProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\Filesystem;
use ReflectionClass;
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
     * 文件操作实例.
     *
     * @var Filesystem
     */
    protected $files;

    protected $header = ['index', 'class', 'status'];

    protected $table = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 发布配置
        $this->call(
            'vendor:publish',
            ['--provider' => 'Tymon\JWTAuth\Providers\LaravelServiceProvider']
        );
        // 生成密钥
        $this->call('jwt:secret');

        // 更新您的用户模型
        $this->updateUserModel(
            config('auth.providers.users.model', '\App\User')
        );

        // 配置身份验证警戒
        $this->mergeAuthConfig(
            config_path('auth.php')
        );

        // 添加一些基本的认证路由
        $this->addAuthRoutes(
            $authController = $this->getDefaultNamespace().'/AuthController'
        );

        // 创建 AuthController
        $this->publishAuthController(
            $authController
        );

        // 更新异常捕获
        $this->updateHandlerRender(
            app_path('Exceptions/Handler.php')
        );

        // 输出渲染表格
        $this->table(
            $this->header,
            $this->table
        );
    }

    /**
     * 更新 User 模型使其实现 JWTSubject 接口。
     */
    protected function updateUserModel($model)
    {
        if (!class_exists($model)) {
            throw new ModelNotFoundException('User 模型不存在，请配置 auth.providers.users.model 参数');
        }

        // 如果还没有实现 JWTSubject 接口
        if (!app()->make($model) instanceof JWTSubject) {
            $this->implementInterface($model);
            $this->addRows([1, 'User model', 'success']);
        } else {
            $this->addRows([1, 'User model', 'unchanged']);
        }
    }

    /**
     * 把 jwt-auth 所需的配置合并到 config/auth.php 中.
     *
     * @param $path
     */
    public function mergeAuthConfig($path)
    {
        $config = $this->files->get($path);

        $search = [
            "'guard' => 'web'",
            "'driver' => 'token'",
        ];
        $replace = [
            "'guard' => 'api'",
            "'driver' => 'jwt'",
        ];
        $config = str_replace($search, $replace, $config, $count);

        if (
            $count > 0 &&
            $this->files->put($path, $config) === strlen($config)
        ) {
            $this->addRows([2, 'Auth config', 'success']);
        } else {
            $this->addRows([2, 'Auth config', 'unchanged']);
        }
    }

    /**
     * 添加基本的验证路由.
     *
     * @param $authController
     */
    protected function addAuthRoutes($authController)
    {
        // 默认的命名空间 'App\Http\Controllers'
        $baseNameSpace = $this->getRouteBaseNamespace();

        $authController = ltrim($authController, '\\/');
        // 去除掉基础部分
        $authController = str_after($authController, $baseNameSpace);
        $authController = ltrim($authController, '\\/');
        // 把命名空间的 / 换回 \
        $authController = str_replace('/', '\\', $authController);

        $routes = <<<routes
        
Route::prefix('auth')->middleware('api')->group(function () {
    Route::post('login', '{$authController}@login');
    Route::post('logout', '{$authController}@logout');
    Route::post('refresh', '{$authController}@refresh');
    Route::post('me', '{$authController}@me');
});
routes;

        $routeFile = base_path('routes/api.php');

        // 文件中没有当前内容才写入
        if (!$this->hasContentInFile($routeFile, $routes)) {
            // 写入到 api 文件
            $this->files->append(
                $routeFile,
                $routes
            );

            $this->addRows([3, 'Route api', 'success']);
        } else {
            $this->addRows([3, 'Route api', 'unchanged']);
        }
    }

    /**
     * 让模型实现接口，增加方法
     * 并写入到原来的模型文件。
     *
     * @param $model
     */
    protected function implementInterface($model)
    {
        // 根据命名空间得到文件路径
        $path = $this->getPath($model);

        // 获取 User 模型的命名空间
        $modelNameSpace = $this->getNamespace(
            $this->qualifyClass($model)
        );

        $namespace = <<<namespace
namespace {$modelNameSpace};

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable  implements JWTSubject
namespace;

        $methods = <<<'method'
        
    /**
     * 获取将存储在JWT主题声明中的标识符。
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
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

    /**
     * 发布有关验证的控制器.
     *
     * @param $authController
     */
    protected function publishAuthController($authController)
    {
        // 创建基类
        $this->createBase(
            function ($class) {
                return function () use ($class) {
                    $this->addRows([4, $class, 'unchanged']);
                };
            },
            function ($class) {
                return function () use ($class) {
                    $this->addRows([4, $class, 'success']);
                };
            }
        );

        $this->createFromName(
            $authController,
            __DIR__.'/../Auth/AuthController.tpl',
            function () {
                $this->addRows([4, 'AuthController', 'unchanged']);
            },
            function () {
                $this->addRows([4, 'AuthController', 'success']);
            }
        );
    }

    /**
     * 获取 RouteServiceProvider 默认的命名空间.
     *
     * @return mixed
     */
    protected function getRouteBaseNamespace()
    {
        $ref = new ReflectionClass(RouteServiceProvider::class);
        // 获取路由对应的命名空间
        $namespace = $ref->getProperty('namespace');
        // 获取路由服务提供者实例
        $route = app()->make(
            RouteServiceProvider::class,
            ['app' => app()]
        );

        // 设置属性可以访问
        $namespace->setAccessible(true);

        return $namespace->getValue($route);
    }

    /**
     * 修改基础的异常捕获类，使其可以捕获到不存在 tokne 抛出的异常.
     *
     * @param $handlePath
     */
    protected function updateHandlerRender($handlePath)
    {
        $search = <<<'search'
return parent::render($request, $exception);
search;

        $apiController = $this->getFullApiName();

        $replace = <<<replace
if (\$exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
            return (new {$apiController})->setCode(\$exception->getCode())
                ->setMsg("[token]不合法 [{\$exception->getMessage()}]")
                ->toJson();
        }

        return parent::render(\$request, \$exception);
replace;

        $content = $this->files->get($handlePath);

        // 如果已经写入过，不用重复
        if (!$this->hasContentInFile($content, $replace)) {
            $content = str_replace($search, $replace, $content);
            $this->files->put($handlePath, $content);

            $this->addRows([5, 'Handler::render', 'success']);
        } else {
            $this->addRows([5, 'Handler::render', 'unchanged']);
        }
    }

    /**
     * 为表格数据添加一行.
     *
     * @param array $rows
     *
     * @return $this
     */
    protected function addRows(array $rows)
    {
        $this->table[] = $rows;

        return $this;
    }
}
