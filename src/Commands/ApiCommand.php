<?php

namespace DavidNineRoc\ApiHelper\Commands;

trait ApiCommand
{
    /**
     * 获取服务层的命名空间
     * @return string
     */
    protected function getServicesNamespace()
    {
        $namespace = config('apihelper.service_namespace', '\App\Services');
        return trim($namespace, '\\/');
    }

    /**
     * 获取 API 基类的完整名字
     * @return string
     */
    protected function getFullApiName()
    {
        return rtrim($this->getApiNamespace(), '\\/') . '/' . ltrim($this->getApiName(), '\\/');
    }

    /**
     * 获取 Api 基类的命名空间
     * @return string
     */
    protected function getApiNamespace()
    {
        $namespace = config('apihelper.base_api_namespace', '\App\Http\Controllers\Api');
        return ltrim($namespace, '\\/');
    }

    /**
     * 获取 Api 基类的名字
     * @return string
     */
    protected function getApiName()
    {
        return config('apihelper.base_api_name', 'ApiController');
    }
}