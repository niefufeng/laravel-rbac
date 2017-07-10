<?php

namespace NieFufeng\LaravelRbac\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface UserInterface
{
    public function roles();

    public function hasRoles($roles, bool $requireAll = false);

    public function hasPermissions($permissions, bool $requireAll = false);

    public function cachedRoles();

    public function forgetCachedRoles();

    public function attachRoles($roles);

    public function detachRoles($roles);
}