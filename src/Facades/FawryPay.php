<?php

namespace MTGofa\FawryPay\Facades;

use Illuminate\Support\Facades\Facade;

class FawryPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fawrypay';
    }
}
