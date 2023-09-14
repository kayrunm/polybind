<?php

namespace Kayrunm\Polybind\Support;

use Closure;
use Exception;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kayrunm\Polybind\Contracts\Type as TypeContract;
use Kayrunm\Polybind\Exceptions\ParameterNotFound;
use Kayrunm\Polybind\Types\IntersectionType;
use Kayrunm\Polybind\Types\Type;
use Kayrunm\Polybind\Types\UnionType;
use ReflectionClass;
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

class ParameterResolver
{
    public function getParameterType(string $name, Route $route): ?TypeContract
    {
        $uses = $route->getAction('uses');

        $parameters = $uses instanceof Closure
            ? $this->getParametersFromClosure($uses)
            : $this->getParametersFromController($uses);

        /** @var ReflectionParameter|null $parameter */
        $parameter = $parameters->first(fn (ReflectionParameter $reflection) => $reflection->getName() === $name);

        if (! $type = $parameter?->getType()) {
            return null;
        }

        return $this->resolveTypes($type);
    }

    private function getParametersFromClosure(Closure $uses): Collection
    {
        return Collection::make(
            (new ReflectionFunction($uses))->getParameters()
        );
    }

    private function getParametersFromController(mixed $uses): Collection
    {
        [$controller, $method] = Str::parseCallback($uses);

        return Collection::make(
            (new ReflectionMethod($controller, $method))->getParameters()
        );
    }

    private function resolveTypes(ReflectionType $type): TypeContract
    {
        return match (get_class($type)) {
            ReflectionNamedType::class => new Type($type->getName()),
            ReflectionUnionType::class => new UnionType(array_map($this->resolveTypes(...), $type->getTypes())),
            ReflectionIntersectionType::class => new IntersectionType(array_map($this->resolveTypes(...), $type->getTypes())),
            default => throw new Exception('Unknown type'),
        };
    }
}
