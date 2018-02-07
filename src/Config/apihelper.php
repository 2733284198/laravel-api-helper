<?php

return [
    /**
     * 服务层的命名空间
     */
    'service_namespace' => '\App\Services',

    /**
     * API 基类的命名空间
     */
    'base_api_namespace' => '\App\Http\Controllers\Api',
    /**
     * API 基类的名字
     */
    'base_api_name' => 'ApiController',

    /**
     * php artisan make:apiController 创建控制器的基础目录
     */
    'controller_namespace' => '\Http\Controllers'
];
