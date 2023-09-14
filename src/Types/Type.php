<?php

namespace Kayrunm\Polybind\Types;

use Kayrunm\Polybind\Contracts\Type as TypeContract;

readonly class Type implements TypeContract
{
    public function __construct(private string $type)
    {
        // ...
    }

    /**
     * @param  object|string  $match
     * @return bool
     */
    public function matches(object|string $match): bool
    {
        $match = is_object($match) ? get_class($match) : $match;

        return is_a($match, $this->type, true);
    }

    public function toArray(): array
    {
        return [$this->type];
    }
}
