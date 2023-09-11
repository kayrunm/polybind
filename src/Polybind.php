<?php

namespace Kayrunm\Polybind;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class Polybind
{
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     *
     * @throws ModelNotFoundException<Model>
     */
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var class-string<Model>|null $class */
        if (! $class = $this->resolveModelClass($request)) {
            return null;
        }

        $model = $class::query()->findOrFail($request->route('model_id'));

        return $next($request);
    }

    /**
     * @param  Request  $request
     * @return class-string<Model>|null
     */
    private function resolveModelClass(Request $request): ?string
    {
        if (! $type = $request->route('model_type')) {
            return null;
        }

        if (class_exists($type) && is_a($type, Model::class, true)) {
            return $type;
        }

        if ($type = Relation::getMorphedModel($type)) {
            return $type;
        }

        return null;
    }
}
