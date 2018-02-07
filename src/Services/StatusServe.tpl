<?php

namespace DummyServicesNamespace;

use Illuminate\Http\Response;

class StatusServe extends Response
{
    /**
     * 通过状态码获取默认的响应消息。
     * @param $statusCode
     * @return string
     */
    public static function getStatusMsg($statusCode)
    {
        return array_key_exists($statusCode, self::$statusTexts) ? self::$statusTexts[$statusCode] : 'SUCCESS';
    }
}