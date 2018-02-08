# api-helper
****
## Requirement
* Laravel 5.5
## Install
```php
composer require davidnineroc/laravel-api-helper
```
## Usage
* 发布文件 (开始之前你完全可以把`app\Http\Controllers`目录删除了，然后按需配置)
```php
php artisan vendor:publish --provider=DavidNineRoc\ApiHelper\ApiServiceProvider
```
* 创建一个控制器
```php
php artisan make:apiController UserController --resource
```
* 快速完成登录相关 (基于 [jwt-auth](https://github.com/tymondesigns/jwt-auth))
```php
php artisan make:apiAuth
```
> `make:apiAuth` 会产生一下事情：
> 1. 发布`config/jwt.php`配置文件
> 2. `.env`文件生成秘钥
> 3. 修改`User`模型使其实现`JWTSubject`接口
> 4. 更新`config/auth.php`文件
> 5. 在`routes/api.php`增加相关路由
> 6. 生成`AuthController`，具体目录查看`config/apihelper.php`配置
> 7. 访问`domain/api/auth/login`便可以进行登录了
## Example
```php
<?php

namespace App\Http\Controllers\Api;

class UserController extends ApiController
{
    public function error()
    {
        $this->notFound('请求数据不存在');
    }
        
    public function login()
    {
        // 表单验证失败
        if (false) {
            return $this->badRequest('手机号不存在');
        }
        
        // 身份验证失败
        return $this->unAuthorized('账号或者密码错误');
    }
    
    public function store()
    {
        $user = User::create([]);
        
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