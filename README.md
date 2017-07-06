# Laravel5 Rbac

`laravel-rbac`是一个基于`Laravel5`的`RBAC`（Role-Based Access Control）扩展包。

## 版本信息

Rbac   | Laravel | PHP
:------|:--------|:--------
0.0.x  |  >=5.x  | >=7.1

## 安装方式

直接通过命令行来快速安装：

```
composer require niefufeng/laravel-rbac
```

或者在`composer.json`的`require`里加入：

```json
{
    "require": {
        "huang-yi/rbac-laravel": "1.0.*"
    }
}
```

然后执行`composer install`。

## 配置

首先在`config/app.php`里的`providers`里添加：

```php
[
    'providers' => [
        NieFufeng\LaravelRbac\ServiceProvider::class,
    ]
]
```

然后发布配置文件和migration文件，在命令行执行：

```
php artisan vendor:publish
```

发布之后根据自己实际情况配置`config/rbac.php`文件。

执行数据库迁移，在命令行执行：

```
php artisan migrate
```

接下来，打开项目的`User`模型，需要使用`NieFufeng\LaravelRbac\Traits\UserTrait`和实现`NieFufeng\LaravelRbac\Contracts\UserInterface`：

```php

<?php

namespace App;

use NieFufeng\LaravelRbac\Contracts\UserInterface;
use NieFufeng\LaravelRbac\Traits\UserTrait;

class User extends Authenticatable implements UserInterface
{
    use UserTrait;
}
```

## 食用方式

```php
$user = App\User::first();

// 新建权限
$permission = NieFufeng\LaravelRbac\Models\Permission::create([
    'name' => '新建用户',
    'slug' => 'create-users',
    'description' => '新建用户'
]);

// 新建角色
$role = NieFufeng\LaravelRbac\Models\Role::create([
    'name' => '管理员',
    'slug' => 'admin',
    'description' => '牛逼哄哄的管理员'
]);

// 给角色赋予权限，接受array、Collection、int和Permission
$role->attachPermissions($permission);

// 给用户赋予角色
$user->attachRoles($role);

// 判断用户是否有xx角色
$user->hasRoles('admin');//true

// hasPermissions和may是一个方法
$user->hasPermissions('create-users');//true
$user->may('create-users');//true

// 剥夺角色
$user->detachRoles($role);

// 获取用户的所有角色信息
$user->roles;
// 从缓存里获取用户的所有角色信息
$user->cachedRoles();
// 从缓存里清除用户的角色信息（执行attachRoles和detachRoles会自动清除）
$user->forgetCachedRoles();
```

当然，扩展目前自带了两个中间件，可以按照需要在`app/Http/Kernel.php`里加入：

```php
protected $routeMiddleware = [
    'permissions' => NieFufeng\LaravelRbac\Middleware\Permission::class,
    'role' => NieFufeng\LaravelRbac\Middleware\Role::class
];
```

然后即可在路由中使用：

```php
Route::get('/', [
    'users' => 'XyzController@hehe',
    'middleware' => 'role:admin,super-admin'
]);
```

## blade支持

```php
//判断是否有角色，多个角色用半角,隔开
@hasRole('admin,super-admin')
如果你看到这段话，说明你是管理员。
@endHasRole

//判断是否有权限，多个权限用半角,隔开
@hasPermission('create-users')
<button>新建用户</button>
@endHasPermission
```

## 支持

Bugs和问题可提交至[Github](https://github.com/niefufeng/laravel-rbac)

Email：[niefufeng@gmail.com](mailto:niefufeng@gmail.com)

QQ：7547811

## License

当然是选择 [MIT license](http://opensource.org/licenses/MIT) 啦~