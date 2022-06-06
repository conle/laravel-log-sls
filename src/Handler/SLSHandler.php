<?php

namespace Conle\LaravelLogSLS\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Conle\LaravelLogSLS\Helpers\ArrayHelper;
use Monolog\Logger;

class SLSHandler extends AbstractProcessingHandler
{
    protected $store;

    public function __construct(string $store = null, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->store = $store;
    }

    protected function write(array $record): void
    {
        $context = ArrayHelper::getValue($record, 'context', []);
        $exception = ArrayHelper::getValue($context, 'exception');
        if ($exception && $exception instanceof \Exception) {
            $context = [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }
        if ($this->store) {
            app('slsLog')->loadConfig($this->store);
        }
        $datetime = ArrayHelper::getValue($record, 'datetime');
        app('slsLog')->putLogs([
            'level' => ArrayHelper::getValue($record, 'level_name', 'INFO'),
            'env' => ArrayHelper::getValue($record, 'channel', ''),
            'message' => ArrayHelper::getValue($record, 'message', ''),
            'context' => json_encode($context),
            'datetime' => $datetime ? $datetime->format('Y-m-d H:i:s') : '',
            'extra' => json_encode(ArrayHelper::getValue($record, 'extra', [])),
        ]);
    }
}
