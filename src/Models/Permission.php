<?php

namespace NieFufeng\LaravelRbac\Models;

use NieFufeng\LaravelRbac\Contracts\PermissionInterface;

/**
 * Class Permission
 * @package NieFufeng\LaravelRbac\Models
 * @property-read string $name 权限的名称
 * @property-read string $slug 权限的slug
 * @property-read string $description 权限的介绍
 * @property-read \Illuminate\Database\Eloquent\Collection|Role[] $roles 有这个权限的角色
 */
class Permission extends Model implements PermissionInterface
{
    protected $table = 'permissions';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Permission $permission) {
            $permission->roles()->sync([]);
            return true;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('rbac.eloquent.role'), 'role_permissions', 'permission_id', 'role_id');
    }

    /**
     * @param $roles
     * @return array
     */
    public function syncRoles($roles)
    {
        return $this->roles()->sync($roles);
    }

    /**
     * @param $roles
     * @return array
     */
    public function syncRolesWithoutDetaching($roles)
    {
        return $this->roles()->syncWithoutDetaching($roles);
    }
}