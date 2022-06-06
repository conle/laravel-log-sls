## Laravel Log SLS

> laravel 日志新增sls通道 <br>
> 新增php8.x支持<br>
> 使用最新的sls Sdk<br>

### 安装

```shell
# 安装
$ composer require conle/laravel-log-sls
```

##### Laravel

```php
# 1、生成配置文件
$ php artisan vendor:publish --tag="sls"

# 2、修改配置文件 /config/sls.php 或在 /.env 文件中添加配置
ALI_ACCESS_KEY_ID=
ALI_ACCESS_KEY_SECRET=
ALI_SLS_END_POINT=
ALI_SLS_PROJECT=
SLS_LOG_STORE_DEFAULT= #默认logStore
SLS_TOPIC=  #可选
SLS_SOURCE=  #可选
SLS_ERROR_LOG_CHANNEL= #可选[默认-daily]

# 3、修改 /config/logging.php 配置，channels 中增加 sls，以下方式二选一；
    
## 3.1 修改 /.env 中 LOG_CHANNEL 为 stack，stack.channels 增加 sls，建议使用此方式，可配置 store
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'sls'],  //增加 'sls'
            'ignore_exceptions' => false,
        ],
        ......

        'sls' => [
            'driver' => 'monolog',
            'handler' => Conle\LaravelLogSLS\Handler\SLSHandler::class,
            'level'  => 'debug',
            'with' => [    // 可选项：当使用多个存储时可配置此参数
                'store' => 'default'    // 对应 sls.php 配置的 stores 的 key
            ]
        ],

## 3.2 修改 .env 中 LOG_CHANNEL 为 sls
    'channels' => [
        ......

        'sls' => [
            'driver' => 'daily',
            'level'  => 'debug',
            'path' => storage_path('logs/laravel.log'),
            'tap'  => [Conle\LaravelLogSLS\Logging\SLSFormatter::class],
            'days' => 14,
        ],
```

##### lumen

```php
# 1、将以下代码段添加到 /bootstrap/app.php 文件中的 Providers 部分
$app->register(Conle\LaravelLogSLS\SLSServiceProvider::class);

# 2、在 .env 文件中添加配置
ALI_ACCESS_KEY_ID=
ALI_ACCESS_KEY_SECRET=
ALI_SLS_END_POINT=
ALI_SLS_PROJECT=
SLS_LOG_STORE_DEFAULT= #默认logStore
SLS_TOPIC=  #可选
SLS_SOURCE=  #可选
SLS_ERROR_LOG_CHANNEL= #可选[默认-daily]

# 3、使用 Log::info() 方式时需增加配置文件/config/logging.php，channels 中增加 sls,参考文件/vendor/laravel/lumen-framework/config/logging.php，以下方式二选一；

## 3.1 修改 /.env 中 LOG_CHANNEL 为 stack，stack.channels 增加 sls，建议使用此方式，可配置 store
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'sls'],  //增加 'sls'
        ],
        ......

        'sls' => [
            'driver' => 'monolog',
            'handler' => Conle\LaravelLogSLS\Handler\SLSHandler::class,
            'level'  => 'debug',
            'with' => [    // 可选项：当使用多个存储时可配置此参数
                'store' => 'default'    // 对应 sls.php 配置的 stores 的 key
            ]
        ],

## 3.2 修改 /.env 中 LOG_CHANNEL 为 sls
    'channels' => [
        ......

        'sls' => [
            'driver' => 'daily',
            'level'  => 'debug',
            'path' => storage_path('logs/lumen.log'),
            'tap'  => [Conle\LaravelLogSLS\Logging\SLSFormatter::class],
            'days' => 14,
        ],
```


## 项目依赖

| 依赖                         | 仓库地址                                                 | 备注 |
| :--------------------------- | :------------------------------------------------------- | :--- |
| conle/php-aliyun-sls-log | https://github.com/conle/php-aliyun-sls-log | 支持php8   |

### 备注

1、、from [seffeng/laravel-sls](https://github.com/seffeng/laravel-sls) 。

