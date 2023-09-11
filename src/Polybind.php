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
    public function handle(
        Request $request,
        Closure $next,
        string $modelTypeParam = 'model_type',
        string $modelIdParam = 'model_id',
        string $modelParam = 'model',
    ): mixed {
        /** @var class-string<Model>|null $class */
        $class = $this->resolveModelClass($request, $modelTypeParam);

        $model = $class::query()->findOrFail($request->route($modelIdParam));

        /** @var Route $route */
        $route = $request->route();

        $route->forgetParameter($modelTypeParam);
        $route->forgetParameter($modelIdParam);
        $route->setParameter($modelParam, $model);

        return $next($request);
    }

    /**
     * @param  Request  $request
     * @param  string  $param
     * @return class-string<Model>
     */
    private function resolveModelClass(Request $request, string $param): string
    {
        if (! $type = $request->route($param)) {
            throw PolybindException::typeNotFound($param);
        }

        if ($class = Relation::getMorphedModel($type)) {
            return $class;
        }

        throw (new ModelNotFoundException())->setModel($type);
    }
}
