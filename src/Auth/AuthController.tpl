<?php

namespace DummyNamespace;

use DummyApiNamespace\DummyApiName;

class AuthController extends DummyApiName
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

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
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ])->toJson();
    }
}