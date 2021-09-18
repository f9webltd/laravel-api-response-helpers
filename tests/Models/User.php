<?php

namespace F9Web\ApiResponseHelpers\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $guarded = [];

    /** @var string */
    protected $table = 'users';
}