<?php

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model implements HasAuthor
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'uuid',
    ];
}
