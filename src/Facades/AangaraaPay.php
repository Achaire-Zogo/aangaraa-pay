<?php

namespace Aangaraa\Pay\Facades;

use Illuminate\Support\Facades\Facade;

class AangaraaPay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'aangaraa-pay';
    }
}
