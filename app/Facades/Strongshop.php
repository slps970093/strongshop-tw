<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Strongshop extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'strongshop';
    }

}
