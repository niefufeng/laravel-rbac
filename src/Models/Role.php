<?php

namespace NieFufeng\LaravelRbac\Models;

use Illuminate\Database\Eloquent\Collection;
use NieFufeng\LaravelRbac\Contracts\RoleInterface;

/**
 * Class Role
 * @package NieFufeng\LaravelRbac\Models
 * @property-read string $name 角色的名称
 * @property-read string $slug 角色的slug
 * @property-read string $description 角色的介绍
 * @property-read \Illuminate\Database\Eloquent\Collection|Permission[] $permissions 角色所拥有的权限
 * @property-read \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[] $users 用这个角色的用户
 */
class Role extends Model implements RoleInterface
{
    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Role $role) {
            $role->permissions()->sync([]);
            $role->forgetCachedPermissions();
            return true;
        });
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('rbac.eloquent.user'), 'user_roles', 'role_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(config('rbac.eloquent.permission'), 'role_permissions', 'role_id', 'permission_id');
    }

    /**
     * @param $permissions
     */
    public function attachPermissions($permissions)
    {
        $this->permissions()->attach($permissions);
    }

    /**
     * @param $permissions
     */
    public function detachPermissions($permissions)
    {
        $this->permissions()->detach($permissions);
    }

    /**
     * @param $permissions
     * @return array
     */
    public function syncPermissions($permissions)
    {
        return $this->permissions()->sync($permissions);
    }

    /**
     * @param $permissions
     * @return array
     */
    public function syncPermissionsWithoutDetaching($permissions)
    {
        return $this->permissions()->syncWithoutDetaching($permissions);
    }

    /**
     * @param $users
     */
    public function attachUsers($users)
    {
        $this->users()->attach($users);
    }

    /**
     * @param $users
     */
    public function detachUsers($users)
    {
        $this->users()->detach($users);
    }

    /**
     * @param $users
     * @return array
     */
    public function syncUsers($users)
    {
        return $this->users()->sync($users);
    }

    /**
     * @param $users
     * @return array
     */
    public function syncUsersWithoutDetaching($users)
    {
        return $this->users()->syncWithoutDetaching($users);
    }

    /**
     * @return Collection|Permission[]
     */
    public function cachedPermissions(): Collection
    {
        $cacheKey = 'laravel_rbac_permissions_for_role_' . $this->getKey();

        return \Cache::tags('role_permissions')->remember($cacheKey, config('rbac.cache.ttl'), function () {
            return $this->permissions()->get();
        });
    }

    /**
     * 删除（当前角色）缓存的权限数据
     */
    public function forgetCachedPermissions()
    {
        $cacheKey = 'laravel_rbac_permissions_for_role_' . $this->getKey();

        \Cache::tags('role_permissions')->forget($cacheKey);
    }
}