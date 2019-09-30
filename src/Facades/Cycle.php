<?php


namespace Laracycle\Facades;


use Illuminate\Support\Facades\Facade;

class Cycle extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cycle';
    }
}
