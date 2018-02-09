<?php

namespace DummyNamespace;

use Illuminate\Http\Request;
use DummyApiNamespace\DummyApiName;

class AuthController extends ApiController
{
    /**
     * 创建一个新的 AuthController 实例。
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['login']]);
    }

    /**
     * 通过给定的凭据获得一个 JWT。
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        /**
         * 在这里进行了表单验证，不需要做什么，
         * 因为已经在异常捕获了表单验证失败，
         * 并且默认返回第一个错误消息
         * 当然，你也可以使用表单请求验证，
         * 注入一个表单请求类来完成验证
         */
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->unAuthorized();
        }

        return $this->respondWithToken($token);
    }

    /**
     * 获取经过身份验证的用户。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->setData(auth()->user())->toJson();
    }

    /**
     * 将用户注销(使令牌无效)。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->setMsg('注销成功')->toJson();
    }

    /**
     * 刷新令牌。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * 获取令牌数组结构。
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return $this->setData([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ])->toJson();
    }
}