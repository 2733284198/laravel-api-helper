### APi

## Install
```php
composer require davidnineroc/laravel-api-helper
```
## Usage
* 生成配置文件
```php
php artisan vendor:publish --tag=config
```
* 生成基础 API 类
```php
php artisan vendor:publish --provider=DavidNineRoc\\ApiHelper\\ApiServiceProvider
```
* 生成 API 控制器
```php
php artisan make:apiController UserController --resource
```