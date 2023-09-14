<?php

namespace Kayrunm\Polybind\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class InvalidModelType extends PolybindException
{
    public static function make(Model $model): self
    {
        $type = get_class($model);

        return new self("Model of type [{$type}] cannot be resolved for this route.");
    }
}
