### api-helper

## Install
```php
composer require davidnineroc/laravel-api-helper
```
## Usage
* 可以把`app/Http/Controllers`这个目录删除了
* 生成配置文件 (按需配置)
```php
php artisan vendor:publish --tag=config
```
* 创建一个控制器
```php
php artisan make:apiController UserController --resource
```
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
## Notices
* 如果在控制器中找不到`create`和`edit`方法，不要惊讶，因为`API`开发中不需要这两个方法，请配合使用`Route::apiResource();`
* 使用`Eloquent: API Resources`转换模型数据