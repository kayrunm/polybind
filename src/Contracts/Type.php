<?php

namespace Kayrunm\Polybind\Contracts;

interface Type
{
    /**
     * @param  object|class-string $match
     * @return bool
     */
    public function matches(object|string $match): bool;

    /**
     * @return array<string>
     */
    public function toArray(): array;
}
