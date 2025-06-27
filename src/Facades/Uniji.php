<?php

namespace DevOpDan\Uniji\Facades;

use Illuminate\Support\Facades\Facade;

class Uniji extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Uniji';
    }
}
