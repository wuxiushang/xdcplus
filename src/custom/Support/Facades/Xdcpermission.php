<?php

namespace Custom\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 */
class Xdcpermission extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return "permission";
    }
}
