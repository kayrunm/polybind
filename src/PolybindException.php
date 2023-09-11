<?php

namespace Kayrunm\Polybind;

use Exception;

class PolybindException extends Exception
{
    public static function typeNotFound(): self
    {
        return new self('The `model_type` route parameter was not found in this request.');
    }
}
