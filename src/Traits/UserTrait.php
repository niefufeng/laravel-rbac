<?php

namespace NieFufeng\LaravelRbac\Traits;

use Illuminate\Database\Eloquent\Collection;
use NieFufeng\LaravelRbac\Models\Role;

trait UserTrait
{
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            if (!method_exists(static::class, 'bootSoftDeletes')) {
                $user->roles()->sync([]);
            }

            return true;
        });
    }

    /**
     * 用户与Role的关联
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('rbac.eloquent.role'), 'user_roles', 'user_id', 'role_id');
    }

    public function save(array $options = [])
    {
        parent::save($options);
        $this->forgetCachedRoles();
    }

    public function delete(array $options = [])
    {
        parent::delete($options);
        $this->forgetCachedRoles();
    }

    public function restore()
    {
        parent::restore();
        $this->forgetCachedRoles();
    }

    /**
     * 删除Roles的缓存
     */
    public function forgetCachedRoles()
    {
        \Cache::tags('user_roles')->forget('laravel_rbac_roles_for_user_' . $this->getKey());
    }

    /**
     * 缓存用户的Roles
     * @return Collection|Role[]
     */
    public function cachedRoles()
    {
        $cacheKey = 'laravel_rbac_roles_for_user_' . $this->getKey();

        return \Cache::tags('user_roles')->remember($cacheKey, config('rbac.cache.ttl'), function () {
            return $this->roles()->get();
        });
    }

    /**
     * 判断用户是否有角色
     * @param string|array|Collection $roles 需要判断的角色
     * @param bool $requireAll 当传入多个角色时，是否需要验证所有角色都存在
     * @return bool
     */
    public function hasRoles($roles, bool $requireAll = false)
    {
        if (is_string($roles)) {
            $requireAll = false;
            $roles = explode(',', $roles);
        }

        foreach ($roles as $role) {
            $hasRole = array_has($this->cachedRoles()->pluck('slug')->toArray(), $role);

            if ($hasRole && !$requireAll) {
                return true;
            } elseif (!$hasRole && $requireAll) {
                return false;
            }
        }
        return $requireAll;
    }

    /**
     * 判断用户是否有某个权限
     * @param string|array|Collection $permissions 需要判断的权限
     * @param bool $requireAll 单传入多个权限时，是否需要验证所有权限都存在
     * @return bool
     */
    public function hasPermissions($permissions, bool $requireAll = false)
    {
        if (is_string($permissions)) {
            $requireAll = false;
            $permissions = explode(',', $permissions);
        }

        $userPermissions = $this->cachedRoles()
            ->map(function (Role $role) {
                return $role->cachedPermissions();
            })
            ->flatten()
            ->pluck('slug')
            ->unique()
            ->values();

        foreach ($permissions as $permission) {
            $hasPermission = $userPermissions->contains($permission);

            if ($hasPermission && !$requireAll) {
                return true;
            } elseif (!$hasPermission && $requireAll) {
                return false;
            }
        }
        return $requireAll;
    }

    /**
     * 判断用户是否有某个权限，因为Laravel的User模型自带了can方法，所以命名为may
     * @param string|array|Collection $permissions 需要判断的权限
     * @param bool $requireAll 单传入多个权限时，是否需要验证所有权限都存在
     * @return bool
     */
    public function may($permissions, bool $requireAll = false)
    {
        return $this->hasPermissions($permissions, $requireAll);
    }

    public function attachRoles($roles)
    {
        $this->roles()->attach($roles);
        $this->forgetCachedRoles();
    }

    public function detachRoles($roles)
    {
        $this->roles()->detach($roles);
        $this->forgetCachedRoles();
    }

    public function syncRoles($roles)
    {
        $this->roles()->sync($roles);
        $this->forgetCachedRoles();
    }
}