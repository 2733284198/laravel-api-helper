<?php

namespace DavidNineRoc\ApiHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

class MakeAuthJwt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:apiAuth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建 jwt 方式登录验证';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 发布配置
        $this->call('vendor:publish', [ '--provider' => 'Tymon\JWTAuth\Providers\LaravelServiceProvider']);
        // 生成密钥
        $this->call('jwt:secret');
        // 合并 config/auth.php 配置
        $this->mergeAuthConfig(
            config_path('auth.php')
        );
    }

    public function mergeAuthConfig($path)
    {
        $files = new Filesystem();

        $config = $files->get($path);

        $search  = [
            "'guard' => 'web'",
            "'driver' => 'token'"
        ];
        $replace = [
            "'guard' => 'api'",
            "'driver' => 'jwt'"
        ];
        $config = str_replace($search, $replace, $config, $count);

        if (
            $count > 0 &&
            $files->put($path, $config) == strlen($config)
        ) {
            $this->info('auth configure success');
        } else {
            $this->info('please configure manually config/auth.php');
        }
    }
}
