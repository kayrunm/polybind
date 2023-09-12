<?php

namespace Kayrunm\Polybind;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

class PolybindServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/polybind.php' => config_path('polybind.php'),
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/polybind.php', 'polybind');
    }
}
