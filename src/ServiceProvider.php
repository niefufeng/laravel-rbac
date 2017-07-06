<?php

namespace NieFufeng\LaravelRbac;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        $this->publishConfigs();

        $this->publishMigrations();
    }

    public function register()
    {
        $this->setupConfig();

        $this->registerRbac();

        $this->registerAliases();

        $this->registerBladeDirectives();
    }

    protected function publishConfigs()
    {
        if (!file_exists(config_path('/rbac.php'))) {
            $this->publishes([
                dirname(__DIR__) . '/config/rbac.php' => config_path('/rbac.php')
            ], 'config');
        }
    }

    protected function setupConfig()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/rbac.php', 'rbac');
    }

    protected function publishMigrations()
    {
        // 理论上只要一个类不存在，其它三个应该也不会存在。。。吧？
        if (!class_exists('CreateRbacRoles')) {
            $datePrefix = date('Y_m_d_His');
            $root = dirname(__DIR__);
            $this->publishes([
                $root . '/database/migrations/create_rbac_roles.stub' => database_path("/migrations/{$datePrefix}_create_rbac_roles.php"),
                $root . '/database/migrations/create_rbac_permissions.stub' => database_path("/migrations/{$datePrefix}_create_rbac_permissions.php"),
                $root . '/database/migrations/create_rbac_role_permission.stub' => database_path("/migrations/{$datePrefix}_create_rbac_role_permission.php"),
                $root . '/database/migrations/create_rbac_user_role.stub' => database_path("/migrations/{$datePrefix}_create_rbac_user_role.php"),
            ], 'migrations');

            Artisan::call('optimize');
        }

    }

    protected function registerRbac()
    {
        $this->app->bind('rbac', Rbac::class);
    }

    protected function registerAliases()
    {
        $this->app->alias(Rbac::class, 'rbac');
    }

    protected function registerBladeDirectives()
    {
        Blade::directive('hasRole', function ($roles) {
            return "<?php if(Auth::check() && Auth::user()->hasRoles({$roles})): ?>";
        });

        Blade::directive('endHasRole', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('hasPermission', function ($permissions) {
            return "<?php if(Auth::check() && Auth::user()->hasPermissions({$permissions})): ?>";
        });

        Blade::directive('endHasPermission', function () {
            return "<?php endif; ?>";
        });
    }
}