<?php

namespace Conle\LaravelLogSLS;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Psr\Log\LoggerInterface;
use Illuminate\Log\Events\MessageLogged;

class Writer implements LoggerInterface
{

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var SLSLog
     */
    private $logger;

    /**
     *
     * @var string
     */
    private $env;

    public function __construct(SLSLog $logger, Dispatcher $dispatcher = null, string $env = '')
    {
        if (isset($dispatcher)) {
            $this->dispatcher = $dispatcher;
        }

        $this->logger = $logger;
        $this->env = $env;
    }

    public function alert($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }


    public function warning($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }


    public function notice($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }


    public function debug($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }


    public function log($level, $message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }


    public function useFiles($path, $level = 'debug')
    {

    }

    public function useDailyFiles($path, $days = 0, $level = 'debug')
    {

    }


    public function emergency($message, array $context = array())
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }


    protected function writeLog($level, $message, $context)
    {
        $this->fireLogEvent($level, $message = $this->formatMessage($message), $context);

        $this->logger->putLogs([
            'level' => $level,
            'env' => $this->env,
            'message' => $message,
            'context' => json_encode($context),
        ]);
    }


    protected function fireLogEvent($level, $message, array $context = [])
    {
        if (isset($this->dispatcher)) {
            $this->dispatcher->dispatch(new MessageLogged($level, $message, $context));
        }
    }


    protected function formatMessage($message)
    {
        if (is_array($message)) {
            return var_export($message, true);
        } elseif ($message instanceof Jsonable) {
            return $message->toJson();
        } elseif ($message instanceof Arrayable) {
            return var_export($message->toArray(), true);
        }

        return $message;
    }
}
