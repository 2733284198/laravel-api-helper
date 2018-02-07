### APi

## Install
```php
composer require davidnineroc/laravel-api-helper
```
## Usage
* 生成配置文件 (按需配置)
```php
php artisan vendor:publish --tag=config
```
* (之后就可以正常的使用了)生成 API 控制器
```php
php artisan make:apiController UserController --resource
```
## Example
```php
<?php

namespace App\Http\Controllers\Api;

class UserController extends ApiController
{
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
        return $this->created('用户注册成功');
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
    
    public function error()
    {
        $this->notFound('请求数据不存在');
    }
    
    public function other()
    {
        $users = User::all();
        
        // 有时，你会需要返回更多的字段
        $this->setCode(200)
            ->setMsg('SUCCESS')
            ->setData($users)
            ->setExtendField('count', $users->count())
            ->setExtendField('field', 'value')
            ->toJson();
    }
}
```