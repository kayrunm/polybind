<?php

namespace Kayrunm\Polybind;

use Closure;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Kayrunm\Polybind\Exceptions\InvalidModelType;
use Kayrunm\Polybind\Exceptions\ParameterNotFound;
use Kayrunm\Polybind\Exceptions\PolybindException;
use Kayrunm\Polybind\Support\ParameterResolver;

class Polybind
{
    public static ?Closure $resolver = null;

    public function __construct(
        private readonly Config $config,
        private readonly ParameterResolver $parameterResolver,
    ) {
        // ...
    }

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string|null  $typeParam
     * @param  string|null  $idParam
     * @param  string|null  $modelParam
     * @throws ParameterNotFound
     * @throws InvalidModelType
     * @return mixed
     *
     */
    public function handle(
        Request $request,
        Closure $next,
        string $typeParam = null,
        string $idParam = null,
        string $modelParam = null,
    ): mixed {
        $typeParam ??= $this->config->get('polybind.defaults.type_param');
        $idParam ??= $this->config->get('polybind.defaults.id_param');
        $modelParam ??= $this->config->get('polybind.defaults.model_param');

        /** @var Route $route */
        $route = $request->route();

        if (! $route->hasParameter($typeParam) || ! $route->hasParameter($idParam)) {
            return $next($request);
        }

        /** @var class-string<Model>|null $class */
        $class = $this->resolveModelClass($request->route($typeParam));

        $model = $this->resolveModel($class, $request->route($idParam));

        if (! $this->isValidType($model, $modelParam, $route)) {
            throw InvalidModelType::make($model);
        }

        $route->forgetParameter($typeParam);
        $route->forgetParameter($idParam);
        $route->setParameter($modelParam, $model);

        return $next($request);
    }

    /**
     * @param  string  $param
     * @throws ParameterNotFound
     * @return class-string<Model>
     *
     */
    private function resolveModelClass(string $param): string
    {
        if ($class = Relation::getMorphedModel($param)) {
            return $class;
        }

        throw (new ModelNotFoundException())->setModel($param);
    }

    /**
     * @param  class-string<Model>  $class
     * @param  string  $modelIdParam
     * @throws ModelNotFoundException
     * @return Model
     *
     */
    private function resolveModel(string $class, string $modelIdParam): Model
    {
        $resolver = self::$resolver ?? $this->config->get('polybind.defaults.resolver');

        return $resolver((new $class())->newQuery(), $modelIdParam);
    }

    private function isValidType(Model $model, mixed $param, Route $route): bool
    {
        $type = $this->parameterResolver->getParameterType($param, $route);

        if (! $type) {
            return true;
        }

        return $type->matches($model);
    }

    public static function setResolver(?Closure $resolver = null): void
    {
        self::$resolver = $resolver;
    }
}
