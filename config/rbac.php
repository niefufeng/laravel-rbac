<?php

return [
    'connection' => null,//默认使用database配置里的default

    'cache' => [
        'ttl' => 1440,//缓存一天
    ],

    'eloquent' => [
        'user' => 'App\User',

        'role' => \NieFufeng\LaravelRbac\Models\Role::class,

        'permission' => \NieFufeng\LaravelRbac\Models\Permission::class,
    ],

];