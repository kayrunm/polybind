<?php

namespace Tests\Concerns;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Kayrunm\Polybind\Polybind;
use Tests\Fixtures\Controllers\BasicController;
use Tests\Fixtures\Controllers\SingleActionController;

trait DefinesRoutes
{
    /**
     * Define routes setup.
     *
     * @param  Router  $router
     * @return void
     */
    public function defineRoutes($router): void
    {
        Route::get('/fail/no-model-type', fn () => 'This route does not have a model_type')->middleware('polybind');
        Route::get('/basic/{model_type}/{model_id}', [BasicController::class, 'show'])->middleware('polybind');
        Route::get('/single-action/{model_type}/{model_id}', SingleActionController::class)->middleware('polybind');
        Route::get('/closure/{model_type}/{model_id}', function ($model) {
            return response()->json($model);
        })->middleware('polybind');
    }
}
