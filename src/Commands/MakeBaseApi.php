<?php

namespace DavidNineRoc\ApiHelper\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class MakeBaseApi extends GeneratorCommand
{
    use ApiCommand;

    protected $name = 'make:apiBaseController';
    protected $description = '创建 API 基类控制器';

    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ($this->alreadyExists($this->getNameInput())) {
            // $this->error($this->type.' already exists!');
            return false;
        }

        $this->makeDirectory($path);

        $stub = $this->files->get(
            $this->getStub()
        );

        $this->files->put($path, $this->replaceNamespace($stub, ''));

        $this->info(
            $this->getType() . ' created successfully.'
        );
    }

    protected function getStub()
    {
        if ($this->option('api')) {
            return __DIR__.'/../Controllers/ApiController.tpl';
        } elseif ($this->option('service')) {
            return __DIR__.'/../Services/ResponseServe.tpl';
        }
    }

    protected function getType()
    {
        if ($this->option('api')) {
            return $this->getApiName();
        } elseif ($this->option('service')) {
            return 'ResponseService';
        }
    }

    /**
     * 替换命名空间
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    public function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            [
                'DummyServicesNamespace',
                'DummyApiNamespace',
                'DummyApiName'
            ],
            [
                $this->getServicesNamespace(),
                $this->getApiNamespace(),
                $this->getApiName(),
            ],
            $stub
        );

        return $stub;
    }

    /**
     * 获取控制台命令选项。
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['api', 'a', InputOption::VALUE_NONE, 'Generate API base controller class'],

            ['service', 's', InputOption::VALUE_NONE, 'Generate Response Service class.'],
        ];
    }

}
