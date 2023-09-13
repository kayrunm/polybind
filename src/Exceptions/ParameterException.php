<?php

namespace Kayrunm\Polybind\Exceptions;

class ParameterException extends PolybindException
{
    public static function parameterNotFound(string $param): self
    {
        return new self("The [$param] route parameter was not found in this request.");
    }
}
