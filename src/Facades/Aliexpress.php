<?php

namespace Corals\Modules\Aliexpress\Facades;

use Illuminate\Support\Facades\Facade;

class Aliexpress extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return \Corals\Modules\Aliexpress\Classes\Aliexpress::class;
    }
}
