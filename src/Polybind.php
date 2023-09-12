<?php

namespace Kayrunm\Polybind;

use Closure;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class Polybind
{
    public static ?Closure $resolver = null;

    public function __construct(private readonly Config $config)
    {
        // ...
    }

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string|null  $typeParam
     * @param  string|null  $idParam
     * @param  string|null  $modelParam
     * @return mixed
     *
     * @throws PolybindException
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

        /** @var class-string<Model>|null $class */
        $class = $this->resolveModelClass($request, $typeParam);

        $model = $this->resolveModel($class, $request->route($idParam));

        /** @var Route $route */
        $route = $request->route();

        $route->forgetParameter($typeParam);
        $route->forgetParameter($idParam);
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
        $resolver = self::$resolver ?? $this->config->get('polybind.defaults.resolver');

        return $resolver((new $class)->newQuery(), $modelIdParam);
    }

    public static function setResolver(?Closure $resolver = null): void
    {
        self::$resolver = $resolver;
    }
}
