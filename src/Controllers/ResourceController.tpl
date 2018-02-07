<?php

namespace DummyNamespace;

use Illuminate\Http\Request;
use DummyApiNamespace\DummyApiName;

class DummyClass extends DummyApiName
{
    /**
     * 显示资源列表。
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
    }

    /**
     * 存储一个新创建的资源存储。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源。
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        //
    }

    /**
     * 更新存储中的指定资源。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 从存储区中移除指定的资源。
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        //
    }
}
