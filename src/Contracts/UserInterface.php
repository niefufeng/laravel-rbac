<?php

namespace NieFufeng\LaravelRbac\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface UserInterface
{
    public function roles();

    public function hasRoles($roles, bool $requireAll = false): bool;

    public function hasPermissions($permissions, bool $requireAll = false): bool;

    public function cachedRoles(): Collection;

    public function forgetCachedRoles();

    public function attachRoles($roles);

    public function detachRoles($roles);
}