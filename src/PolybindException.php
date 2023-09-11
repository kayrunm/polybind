<?php

namespace Kayrunm\Polybind;

use Exception;

class PolybindException extends Exception
{
    public static function typeNotFound(string $param): self
    {
        return new self("The [$param] route parameter was not found in this request.");
    }
}
