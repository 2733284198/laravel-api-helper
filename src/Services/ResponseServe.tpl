<?php

namespace DummyServicesNamespace;

use Symfony\Component\HttpFoundation\Response;

trait ResponseServe
{
    protected $code = 0;
    protected $msg = 'SUCCESS';
    protected $data = [];

    protected $extendField = [];

    public function created($msg = '资源创建成功', array $data = [])
    {
        return $this->setCode(Response::HTTP_CREATED)
            ->setMsg($msg)
            ->setData($data)
            ->toJson();
    }


    public function serviceUnavailable($msg = '服务器未知出错')
    {
        return $this->setCode(Response::HTTP_SERVICE_UNAVAILABLE)
            ->setMsg($msg)
            ->toJson();
    }


    public function forbidden($msg = '权限不足')
    {
        return $this->setCode(Response::HTTP_FORBIDDEN)
            ->setMsg($msg)
            ->toJson();
    }


    public function unAuthorized($msg = '身份验证失败')
    {
        return $this->setCode(Response::HTTP_UNAUTHORIZED)
            ->setMsg($msg)
            ->toJson();
    }

    public function notFound($msg = '请求页面找不到')
    {
        return $this->setCode(Response::HTTP_NOT_FOUND)
            ->setMsg($msg)
            ->toJson();
    }

    public function badRequest($msg = '表单验证出错')
    {
        return $this->setCode(Response::HTTP_BAD_REQUEST)
            ->setMsg($msg)
            ->toJson();
    }


    public function toJson($httpStatus = 200, $headers = [])
    {
        return response()->json($this->formatResponse(), $httpStatus, $headers, JSON_UNESCAPED_UNICODE);
    }

    private function formatResponse()
    {
        $response = [
            'code' => $this->code,
            'msg' => $this->msg,
            'data' => $this->data
        ];

        /**
         * 如果有扩展字段，依次加入
         */
        if (! empty($this->extendField)) {
            foreach ($this->extendField as $key => $value) {
                $response[$key] = $value;
            }
        }

        return $response;
    }

    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;

        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function setExtendField($field, $value)
    {
        $this->extendField[$field] = $value;

        return $this;
    }
}
