<?php

namespace DummyNamespace;

use DummyFullModelClass;
use ParentDummyFullModelClass;
use Illuminate\Http\Request;
use DummyApiNamespace\DummyApiName;

class DummyClass extends DummyApiName
{
    /**
     * 显示资源列表。
     *
     * @param  \ParentDummyFullModelClass  $ParentDummyModelVariable
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ParentDummyModelClass $ParentDummyModelVariable)
    {
        //
    }

    /**
     * 存储一个新创建的资源存储。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \ParentDummyFullModelClass  $ParentDummyModelVariable
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ParentDummyModelClass $ParentDummyModelVariable)
    {
        //
    }

    /**
     * 显示指定的资源。
     *
     * @param  \ParentDummyFullModelClass  $ParentDummyModelVariable
     * @param  \DummyFullModelClass  $DummyModelVariable
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ParentDummyModelClass $ParentDummyModelVariable, DummyModelClass $DummyModelVariable)
    {
        //
    }

    /**
     * 更新存储中的指定资源。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \ParentDummyFullModelClass  $ParentDummyModelVariable
     * @param  \DummyFullModelClass  $DummyModelVariable
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ParentDummyModelClass $ParentDummyModelVariable, DummyModelClass $DummyModelVariable)
    {
        //
    }

    /**
     * 从存储区中移除指定的资源。
     *
     * @param  \ParentDummyFullModelClass  $ParentDummyModelVariable
     * @param  \DummyFullModelClass  $DummyModelVariable
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ParentDummyModelClass $ParentDummyModelVariable, DummyModelClass $DummyModelVariable)
    {
        //
    }
}
