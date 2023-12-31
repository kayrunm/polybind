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
        Route::get('/passthru', fn () => response()->noContent())->middleware('polybind');
        Route::get('/passthru/{model_type}', fn () => response()->noContent())->middleware('polybind');

        Route::get('/basic/{model_type}/{model_id}', [BasicController::class, 'show'])->middleware('polybind');
        Route::get('/single-action/{model_type}/{model_id}', SingleActionController::class)->middleware('polybind');
        Route::get('/closure/{model_type}/{model_id}', function ($model) {
            return response()->json($model);
        })->middleware('polybind');

        Route::get('/custom-model-type/{type}/{model_id}', [BasicController::class, 'show'])
            ->middleware('polybind:type');

        Route::get('/custom-model-id/{type}/{id}', [BasicController::class, 'show'])
            ->middleware('polybind:type,id');

        Route::get('/custom-model/{type}/{id}', [BasicController::class, 'showCustom'])
            ->middleware('polybind:type,id,myModel');

        Route::get('/custom/{type}/{uuid}', [BasicController::class, 'showCustom'])
            ->middleware('polybind');

        Route::get('/specific/{model_type}/{model_id}', [BasicController::class, 'showSpecific'])
            ->middleware('polybind');

        Route::get('/union/{model_type}/{model_id}', [BasicController::class, 'showUnion'])
            ->middleware('polybind');

        Route::get('/interface/{model_type}/{model_id}', [BasicController::class, 'showInterface'])
            ->middleware('polybind');
    }
}
