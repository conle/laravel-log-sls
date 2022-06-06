<?php

namespace Conle\LaravelLogSLS\Logging;

use Illuminate\Log\Logger;

class SLSFormatter
{
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new SLSContentFormatter());
        }
    }
}
