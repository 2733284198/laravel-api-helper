<?php

namespace DummyApiNamespace;

use DummyRootNamespaceServices\ResponseServe;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class DummyApiName extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $code = 0;
    protected $msg = 'SUCCESS';
    protected $data = [];

    protected $extendField = [];


    public function created($msg = '资源创建成功', array $data = [])
    {
        return $this->setCode(ResponseServe::HTTP_CREATED)
            ->setMsg($msg)
            ->setData($data)
            ->toJson();
    }


    public function serviceUnavailable($msg = '服务器未知出错')
    {
        return $this->setCode(ResponseServe::HTTP_SERVICE_UNAVAILABLE)
            ->setMsg($msg)
            ->toJson();
    }


    public function forbidden($msg = '权限不足')
    {
        return $this->setCode(ResponseServe::HTTP_FORBIDDEN)
            ->setMsg($msg)
            ->toJson();
    }


    public function unAuthorized($msg = '身份验证失败')
    {
        return $this->setCode(ResponseServe::HTTP_UNAUTHORIZED)
            ->setMsg($msg)
            ->toJson();
    }

    public function notFound($msg = '请求页面找不到')
    {
        return $this->setCode(ResponseServe::HTTP_NOT_FOUND)
            ->setMsg($msg)
            ->toJson();
    }

    public function badRequest($msg = '表单验证出错')
    {
        return $this->setCode(ResponseServe::HTTP_BAD_REQUEST)
            ->setMsg($msg)
            ->toJson();
    }


    public function toJson($httpStatus = 200, $headers = [])
    {
        return response()->json($this->formatResponse(), $httpStatus, $headers, JSON_UNESCAPED_UNICODE);
    }

    private function formatResponse()
    {
        $msg = ($this->msg == 'SUCCESS') ? ResponseServe::getErrorMsg($this->code) : $this->msg;


        $response = [
            'code' => $this->code,
            'msg' => $msg,
            'data' => $this->data
        ];

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
