<?php

namespace NieFufeng\LaravelRbac\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface RoleInterface
{
    public function permissions();

    public function users();

    public function attachPermissions($permissions);

    public function detachPermissions($permissions);

    public function syncPermissions($permissions);

    public function attachUsers($users);

    public function detachUsers($users);

    public function cachedPermissions(): Collection;

    public function forgetCachedPermissions();
}