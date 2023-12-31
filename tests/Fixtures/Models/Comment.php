<?php

namespace Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model implements HasAuthor
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'uuid',
    ];
}
