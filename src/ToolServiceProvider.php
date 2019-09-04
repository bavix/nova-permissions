<?php
namespace Bavix\NovaPermissions;

use Laravel\Nova\Nova;
use Illuminate\Support\Collection;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Bavix\NovaPermissions\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @param Filesystem $filesystem
     *
     * @return void
     */
    public function boot(Filesystem $filesystem): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-permissions');

        $this->publishes([
            __DIR__ . '/../config/permission.php' => config_path('permission.php'),
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/permission.php',
            'permission'
        );
    }

}
