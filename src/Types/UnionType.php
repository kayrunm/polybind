<?php

namespace Kayrunm\Polybind\Types;

use Kayrunm\Polybind\Contracts\Type as TypeContract;

readonly class UnionType implements TypeContract
{
    /**
     * @param array<TypeContract> $types
     */
    public function __construct(private array $types)
    {
        // ...
    }

    public function matches(object|string $match): bool
    {
        $match = is_object($match) ? get_class($match) : $match;

        foreach ($this->types as $type) {
            if ($type->matches($match)) {
                return true;
            }
        }

        return false;
    }

    public function toArray(): array
    {
        return array_reduce($this->types, fn (array $carry, TypeContract $type) => [
            ...$carry,
            ...$type->toArray(),
        ], []);
    }
}
