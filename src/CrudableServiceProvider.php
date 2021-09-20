<?php

namespace berthott\Crudable;

use berthott\Crudable\Exceptions\Handler;
use berthott\Crudable\Facades\Crudable;
use berthott\Crudable\Http\Controllers\CrudController;
use berthott\Crudable\Models\Contracts\Targetable;
use berthott\Crudable\Services\CrudableService;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CrudableServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // bind singleton
        $this->app->singleton('Crudable', function () {
            return new CrudableService();
        });

        // bind exception singleton
        $this->app->singleton(ExceptionHandler::class, Handler::class);

        // add config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'crudable');

        // init targetables
        $this->app->afterResolving(Targetable::class, function (Targetable $targetable) {
            $targetable->initTarget();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // publish config
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('crudable.php'),
        ], 'config');

        // add routes
        Route::group($this->routeConfiguration(), function () {
            foreach (Crudable::getCrudableClasses() as $crudable) {
                Route::apiResource($crudable::newModelInstance()->getTable(), CrudController::class);
            }
        });
    }

    protected function routeConfiguration(): array
    {
        return [
            'middleware' => config('crudable.middleware'),
            'prefix' => config('crudable.prefix'),
        ];
    }
}
