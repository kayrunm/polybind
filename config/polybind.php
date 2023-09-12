<?php

use Illuminate\Database\Eloquent\Builder;

return [

    'defaults' => [

        'type_param' => 'model_type',

        'id_param' => 'model_id',

        'model_param' => 'model',

        'resolver' => fn (Builder $query, $value) => $query->findOrFail($value),

    ],

];
