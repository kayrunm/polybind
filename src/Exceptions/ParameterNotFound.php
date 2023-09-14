<?php

namespace Kayrunm\Polybind\Exceptions;

class ParameterNotFound extends PolybindException
{
    public static function make(string $param): self
    {
        return new self("The [$param] route parameter was not found in this request.");
    }
}
