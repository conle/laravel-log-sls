<?php
namespace Conle\LaravelLogSLS;

use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class SLSServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->registerAliases();
        $this->mergeConfigFrom($this->configPath(), 'sls');
        $this->app->singleton('slsLog', function ($app) {
            $config = $app['config']->get('sls');
            if ($config && is_array($config)) {
                return new SLSLog($config);
            } else {
                throw new \RuntimeException('Please execute the command `php artisan vendor:publish --tag="slslog"` first to  generate sms configuration file.');
            }
        });

        $config = $this->app['config']['sls'];
        $this->app->instance('slsLog.writer', new Writer(app('slsLog'), $this->app['events'], $config['env']));
    }

    public function boot()
    {
        if ($this->app->runningInConsole() && $this->app instanceof LaravelApplication) {
            $this->publishes([$this->configPath() => config_path('sls.php')], 'sls');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('sls');
        }
    }

    protected function registerAliases()
    {
        $this->app->alias('slsLog', SLSLog::class);
        $this->app->alias('slsLog.writer', Writer::class);
    }

    protected function configPath()
    {
        return dirname(__DIR__) . '/config/sls.php';
    }
}
