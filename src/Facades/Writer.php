<?php

namespace Conle\LaravelLogSLS\Facades;

use Illuminate\Support\Facades\Facade;

class Writer extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'slsLog.writer';
    }
}
