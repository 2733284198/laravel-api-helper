# api-helper

<p align="center">
<a href="https://styleci.io/repos/120559524"><img src="https://styleci.io/repos/120559524/shield?branch=master" alt="StyleCI"></a>
<a href="https://packagist.org/packages/davidnineroc/laravel-api-helper"><img src="https://poser.pugx.org/davidnineroc/laravel-api-helper/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/davidnineroc/laravel-api-helper"><img src="https://poser.pugx.org/davidnineroc/laravel-api-helper/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/davidnineroc/laravel-api-helper"><img src="https://poser.pugx.org/davidnineroc/laravel-api-helper/license" alt="License"></a>
</p> 

****
## Requirement
* PHP 7.1
* Laravel 5.5.*
## Installation & Usage
* 使用`composer`安装
    * 先安装**jwt-auth**(等v1稳定版本出来了再依赖)
    ```php
    composer require tymon/jwt-auth v1.0.0-rc.2
    ```
    * 安装**api-helper**
    ```php
    composer require davidnineroc/laravel-api-helper v1.1.8
    ```
* 发布文件 (开始之前你完全可以把`app\Http\Controllers`目录删除了，然后按需配置)
```php
php artisan vendor:publish --provider=DavidNineRoc\ApiHelper\ApiServiceProvider
```
* 创建一个资源控制器
```php
php artisan api:controller UserController --resource
```
* 创建一个 Service
```php
php artisan api:controller UserController --resource
```
## Example
* 快速完成登录相关 (基于 [jwt-auth](https://github.com/tymondesigns/jwt-auth))
```php
php artisan api:auth
```
> `make:apiAuth` 会产生以下事件：
> * 发布`config/jwt.php`配置文件
> * `.env`文件生成秘钥
> 1. 修改`User`模型使其实现`JWTSubject`接口
> 2. 更新`config/auth.php`文件
> 3. 在`routes/api.php`增加相关路由
> 4. 生成`AuthController`，具体目录查看`config/apihelper.php`配置
> 5. 在`app/Exceptions/Handler::render`增加拦截`jwt`，表单验证错误的异常抛出
> ![php artisan make:apiAuth](http://p2uena5sd.bkt.clouddn.com/github/artisan_make_api_auth.png)
> * 访问`domain/api/auth/login`便可以进行登录了(更多路由，请查看`routes/api.php`)
****
```php
<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class UserController extends ApiController
{
    public function error()
    {
        $this->notFound('请求数据不存在');
    }
        
    public function login(Request $request)
    {
        /**
         * 在这里进行了表单验证，不需要做什么，
         * 因为已经在异常捕获了表单验证失败，
         * 并且默认返回第一个错误消息
         * 当然，你也可以使用表单请求验证，
         * 注入一个表单请求类来完成验证
         */
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        // 身份验证失败
        return $this->unAuthorized('账号或者密码错误');
    }
    
    public function store(Request $request)
    {
        $user = User::create($request->all());
        
        return $this->created('用户注册成功', $user);
    }
    
    public function show(User $user)
    {
        if ($notAdmin = true) {
            $this->forbidden('权限不足');
        }
        
        return $this->setCode(200)
                    ->setMsg('SUCCESS')
                    ->setData($user)
                    ->toJson();
    }
    
    public function other()
    {
        $users = User::all();
        
        // 有时，你可能需要返回更多的字段
        $this->setCode(200)
            ->setMsg('SUCCESS')
            ->setData($users)
            ->setExtendField('count', $users->count())
            ->setExtendField('field', 'value')
            ->toJson();
    }
}
```
## Recommend
* 如果在控制器中找不到`create`和`edit`方法，不要惊讶，因为`API`开发中不需要这两个方法，请配合使用`Route::apiResource();`
* 使用`Eloquent: API Resources`转换模型数据
## Errors
* 出现模型修改错误
    * 确保`config/auth.php=>providers=>users=>model`配置正确了`User`模型
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).