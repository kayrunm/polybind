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
        $class = $this->resolveModelClass($request);

        $model = $class::query()->findOrFail($request->route('model_id'));

        /** @var Route $route */
        $route = $request->route();

        $route->forgetParameter('model_type');
        $route->forgetParameter('model_id');
        $route->setParameter('model', $model);

        return $next($request);
    }

    /**
     * @param  Request  $request
     * @return class-string<Model>|null
     */
    private function resolveModelClass(Request $request): string
    {
        if (! $type = $request->route('model_type')) {
            throw PolybindException::typeNotFound();
        }

        if ($class = Relation::getMorphedModel($type)) {
            return $class;
        }

        throw (new ModelNotFoundException())->setModel($type);
    }
}
