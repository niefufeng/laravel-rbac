<?php

namespace NieFufeng\LaravelRbac\Facades;

use Illuminate\Support\Facades\Facade;

class RbacFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rbac';
    }
}