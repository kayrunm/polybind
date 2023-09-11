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
    public static ?Closure $resolver = null;

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

        $model = $this->resolveModel($class, $request->route($modelIdParam));

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

    /**
     * @param  class-string<Model>  $class
     * @param  string  $modelIdParam
     * @return Model
     *
     * @throws ModelNotFoundException
     */
    private function resolveModel(string $class, string $modelIdParam): Model
    {
        $resolver = self::$resolver ?? fn ($query) => $query->findOrFail($modelIdParam);

        return $resolver((new $class)->newQuery(), $modelIdParam);
    }

    public static function setResolver(?Closure $resolver = null): void
    {
        self::$resolver = $resolver;
    }
}
