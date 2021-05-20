<?php
namespace WithKao\Facades\Notify;

use WithKao\LineNotify;
use Illuminate\Support\Facades\Facade;

class Line extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LineNotify::class;
    }
}
