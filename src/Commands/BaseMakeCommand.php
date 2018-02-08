<?php

namespace DavidNineRoc\ApiHelper\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class BaseMakeCommand extends GeneratorCommand
{
    use NameSpaceTrait;

    protected function getStub()
    {
        throw new CommandNotFoundException('please rewrite Exception');
    }

    /**
     * 获取控制器默认命名空间。
     * 参数暂时无用，确定在 App\ 底下。
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('apihelper.controller_namespace', '\App\Http\Controllers');
    }

    /**
     * 重写父类替换方法，因为 ApiController 的命名空间也是动态的。
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    public function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            [
                'DummyNamespace',
                'DummyRootNamespace',
                'NamespacedDummyUserModel',
                'DummyApiNamespace',
                'DummyApiName',
                'DummyServicesNamespace',
            ],
            [
                $this->getNamespace($name),
                $this->rootNamespace(),
                config('auth.providers.users.model'),
                $this->getApiNamespace(),
                $this->getApiName(),
                $this->getServicesNamespace()
            ],
            $stub
        );

        return $this;
    }

    /**
     * 创建基础层的文件。
     * 状态响应码文件
     * JSON 响应类
     * Api 基类。
     */
    protected function createBase()
    {
        $files = [
            'StatusServe' => [
                'full_name' => $this->getServicesNamespace().'/StatusServe',
                'file' => __DIR__.'/../Services/StatusServe.tpl'
            ],
            'ResponseService' => [
                'full_name' => $this->getServicesNamespace().'/ResponseServe',
                'file' => __DIR__.'/../Services/ResponseServe.tpl'
            ],
            $this->getApiName() => [
                'full_name' => $this->getFullApiName(),
                'file' => __DIR__.'/../Controllers/ApiController.tpl'
            ]
        ];

        foreach ($files as $key => $class) {
            $this->createFromName($class['full_name'], $class['file'], $key);
        }
    }

    /**
     * 根据给定的 命名空间，文件路径 提示消息。
     * 先去解析参数的命名空间，得到一个目标路径文件，
     * 然后通过给定的文件路径去读取内容，替换，之后写入到目标路径文件。
     * @param $fullName
     * @param $filePath
     * @param string $typeInfo
     * @return bool
     */
    protected function createFromName($fullName, $filePath, $typeInfo = '')
    {
        // 获取过滤之后的路径
        $name = $this->qualifyClass($fullName);
        // 根据命名空间得到文件路径
        $path = $this->getPath($name);

        if ($this->alreadyExists($fullName)) {
            // $this->error($this->type.' already exists!');
            return false;
        }

        $this->makeDirectory($path);

        $stub = $this->files->get($filePath);

        $this->files->put($path, $this->replaceNamespace($stub, $name)->replaceClass($stub, ''));

        $this->info("{$typeInfo} created successfully.");
    }

    /**
     * 根据根命名空间解析类名和格式。
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }
}