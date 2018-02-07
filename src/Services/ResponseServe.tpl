<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;

class ResponseServe extends Response
{
    public static $errCodes = [
        // 系统码
        '400' => '未知错误',
        '401' => '授权已过期，请重新登录',
        '404' => '找不到请求的内容',
        '500' => '服务器异常',
        '503' => '服务不可用',
    ];

    public static function getErrorMsg($code)
    {
        return isset(self::$errCodes[$code]) ? self::$errCodes[$code] : 'SUCCESS';
    }
}
