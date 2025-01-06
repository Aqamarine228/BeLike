<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\SocialiteProvider as Provider;

class SocialiteProvider extends Model
{

    protected $casts = [
        'provider' => Provider::class,
    ];
}
