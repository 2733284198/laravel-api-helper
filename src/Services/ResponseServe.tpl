<?php

namespace DummyServicesNamespace;

trait ResponseServe
{
    /**
     * 消息状态码
     * @var int
     */
    protected $code = 200;

    /**
     * 消息内容
     * @var string
     */
    protected $msg = '';

    /**
     * 数据
     * @var array
     */
    protected $data = [];

    /**
     * 扩展字段返回
     * @var array
     */
    protected $extendField = [];

    /**
     * 资源创建成功响应
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function created($msg = '资源创建成功', array $data = [])
    {
        return $this->setCode(StatusServe::HTTP_CREATED)
            ->setMsg($msg)
            ->setData($data)
            ->toJson();
    }


    /**
     * 服务器位置错误响应
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function serviceUnavailable($msg = '服务器未知出错')
    {
        return $this->setCode(StatusServe::HTTP_SERVICE_UNAVAILABLE)
            ->setMsg($msg)
            ->toJson();
    }


    /**
     * 权限不足响应
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function forbidden($msg = '权限不足')
    {
        return $this->setCode(StatusServe::HTTP_FORBIDDEN)
            ->setMsg($msg)
            ->toJson();
    }


    /**
     * 身份验证失败响应
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function unAuthorized($msg = '身份验证失败')
    {
        return $this->setCode(StatusServe::HTTP_UNAUTHORIZED)
            ->setMsg($msg)
            ->toJson();
    }

    public function notFound($msg = '请求页面找不到')
    {
        return $this->setCode(StatusServe::HTTP_NOT_FOUND)
            ->setMsg($msg)
            ->toJson();
    }

    /**
     * 表单验证错误响应
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function badRequest($msg = '表单验证出错')
    {
        return $this->setCode(StatusServe::HTTP_BAD_REQUEST)
            ->setMsg($msg)
            ->toJson();
    }

    /**
     * 返回 json 响应数据
     * @param int $httpStatus
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function toJson($httpStatus = 200, $headers = [])
    {
        return response()->json(
            $this->formatResponse(),
            $httpStatus,
            $headers,
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * 把 状态码，响应消息，响应数据，扩展字段（如果有的话）放入同一级数组
     * @return array
     */
    protected function formatResponse()
    {
        // 如果响应消息从未被设置过，则去取默认的消息
        $this->msg = empty($this->msg) ? StatusServe::getStatusMsg($this->code) : $this->msg;

        $response = [
            'code' => $this->code,
            'msg' => $this->msg,
            'data' => $this->data
        ];

        /**
         * 如果有扩展字段，依次加入响应内容中
         */
        if (! empty($this->extendField)) {
            foreach ($this->extendField as $key => $value) {
                $response[$key] = $value;
            }
        }

        return $response;
    }

    /**
     * 设置响应状态码
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * 设置响应消息
     * @param $msg
     * @return $this
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * 设置响应返回数据
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * 设置扩展字段
     * @param $field
     * @param $value
     * @return $this
     */
    public function setExtendField($field, $value)
    {
        $this->extendField[$field] = $value;

        return $this;
    }
}