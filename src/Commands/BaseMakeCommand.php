<?php

namespace DavidNineRoc\ApiHelper\Commands;

use Closure;
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
     * @param string $rootName
     *
     * @return string
     */
    protected function getDefaultNamespace($rootName = '')
    {
        return config('apihelper.controller_namespace', '\App\Http\Controllers');
    }

    /**
     * 重写父类替换方法，因为 ApiController 的命名空间也是动态的。
     *
     * @param string $stub
     * @param string $name
     *
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
                $this->getServicesNamespace(),
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
     * @param Closure $existsRunFunction
     * @param Closure $createdRunFunction
     */
    protected function createBase(Closure $existsRunFunction, Closure $createdRunFunction)
    {
        $files = [
            'StatusServe' => [
                'full_name' => $this->getServicesNamespace().'/StatusServe',
                'file' => __DIR__.'/../Services/StatusServe.tpl',
            ],
            'ResponseService' => [
                'full_name' => $this->getServicesNamespace().'/ResponseServe',
                'file' => __DIR__.'/../Services/ResponseServe.tpl',
            ],
            $this->getApiName() => [
                'full_name' => $this->getFullApiName(),
                'file' => __DIR__.'/../Controllers/ApiController.tpl',
            ],
        ];

        foreach ($files as $key => $class) {

            $this->createFromName(
                $class['full_name'],
                $class['file'],
                $existsRunFunction($key),
                $createdRunFunction($key)
            );
        }
    }

    /**
     * 根据给定的 命名空间，文件路径 提示消息。
     * 先去解析参数的命名空间，得到一个目标路径文件，
     * 然后通过给定的文件路径去读取内容，替换，之后写入到目标路径文件。
     *
     * @param $fullName
     * @param $filePath
     * @param null $existsRunFunction
     * @param null $createdRunFunction
     * @return bool
     */
    protected function createFromName($fullName, $filePath, $existsRunFunction = null, $createdRunFunction = null)
    {
        // 获取过滤之后的路径
        $name = $this->qualifyClass($fullName);
        // 根据命名空间得到文件路径
        $path = $this->getPath($name);

        // 下标
        if ($this->alreadyExists($fullName)) {
            if ($existsRunFunction instanceof Closure) {
                $existsRunFunction();
            }
            return false;
        }

        $this->makeDirectory($path);
        // 获取文件内容
        $stub = $this->files->get($filePath);
        // 写入替换之后的内容
        $this->files->put($path, $this->replaceNamespace($stub, $name)->replaceClass($stub, ''));

        if ($createdRunFunction instanceof Closure) {
            $createdRunFunction();
        }
    }

    /**
     * 根据根命名空间解析类名和格式。
     *
     * @param string $name
     *
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

    /**
     * 内容是否在文件中存在，忽略空白字符
     * @param $file
     * @param $content
     * @return bool
     */
    protected function hasContentInFile($file, $content)
    {
        if ($this->files->isFile($file)) {
            $file = $this->files->get($file);
        }

        // 删除多余的空格，回车
        return strpos($this->trimBlankChar($file), $this->trimBlankChar($content)) !== false;
    }

    /**
     * 删除空白字符。
     * @param $string
     * @return mixed
     */
    protected function trimBlankChar($string)
    {
        return str_replace([
            "\s",
            "\r",
            "\n",
            "\r\n",
            " "
        ], '', $string);
    }
}
