<?php

namespace berthott\Crudable;

use berthott\Crudable\Exceptions\Handler;
use berthott\Crudable\Facades\Crudable;
use berthott\Crudable\Http\Controllers\CrudController;
use berthott\Crudable\Models\Contracts\Targetable;
use berthott\Crudable\Services\CrudableService;
use berthott\Crudable\Services\CrudQueryService;
use berthott\Crudable\Services\CrudRelationsService;
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
        // bind singletons
        $this->app->singleton('Crudable', function () {
            return new CrudableService();
        });
        $this->app->singleton('CrudRelations', function () {
            return new CrudRelationsService();
        });
        $this->app->singleton('CrudQuery', function () {
            return new CrudQueryService();
        });

        // add config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'crudable');
        $this->mergeConfigFrom(__DIR__.'/../config/query-builder.php', 'query-builder');

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
                Route::group(['middleware' => $crudable::middleware()], function () use ($crudable) {
                    $table = $crudable::entityTableName();
                    $crudable::routesBefore();
                    $crudRoutes = $this->getCrudRoutes($crudable::routeOptions());
                    if (in_array('schema', $crudRoutes)) {
                        Route::get("{$table}/schema", [CrudController::class, 'schema'])->name($table.'.schema');
                    }
                    if (in_array('destroy_many', $crudRoutes)) {
                        Route::delete("{$table}/destroy_many", [CrudController::class, 'destroy_many'])->name($table.'.destroy_many');
                    }
                    Route::apiResource($table, CrudController::class, $crudable::routeOptions());
                    $crudable::routesAfter();
                });
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

    /**
     * Get the applicable resource methods.
     */
    protected function getCrudRoutes(array $options): array
    {
        $methods = ['index', 'show', 'store', 'update', 'destroy', 'schema', 'destroy_many'];

        if (isset($options['only'])) {
            $methods = array_intersect($methods, (array) $options['only']);
        }

        if (isset($options['except'])) {
            $methods = array_diff($methods, (array) $options['except']);
        }

        return $methods;
    }
}
