<?php

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'uuid',
    ];
}
