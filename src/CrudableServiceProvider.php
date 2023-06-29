<?php

namespace berthott\Crudable;

use berthott\Crudable\Http\Controllers\CrudController;
use Facades\berthott\Crudable\Services\CrudableService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CrudableServiceProvider extends ServiceProvider
{
    /**
     * Possible routes.
     * 
     * @var string[]
     * @api
     */
    private $routes = [
        'index', 
        'show', 
        'store', 
        'update', 
        'destroy', 
        'schema', 
        'destroy_many',
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // add config
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'crudable');
        $this->mergeConfigFrom(__DIR__.'/../config/query-builder.php', 'query-builder');
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
        foreach (CrudableService::getTargetableClasses() as $crudable) {
            Route::group($this->routeConfiguration(), function () use ($crudable) {
                $crudable::routesBefore();
                Route::group(['middleware' => $crudable::middleware()], function () use ($crudable) {
                    $table = $crudable::entityTableName();
                    $crudRoutes = $this->getCrudRoutes($crudable::routeOptions());
                    if (in_array('schema', $crudRoutes)) {
                        Route::get("{$table}/schema", [CrudController::class, 'schema'])->name($table.'.schema');
                    }
                    if (in_array('destroy_many', $crudRoutes)) {
                        Route::delete("{$table}/destroy_many", [CrudController::class, 'destroy_many'])->name($table.'.destroy_many');
                    }
                    Route::apiResource($table, CrudController::class, $crudable::routeOptions());
                });
                $crudable::routesAfter();
            });
        }
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
        $methods = $this->routes;

        if (isset($options['only'])) {
            $methods = array_intersect($methods, (array) $options['only']);
        }

        if (isset($options['except'])) {
            $methods = array_diff($methods, (array) $options['except']);
        }

        return $methods;
    }
}
