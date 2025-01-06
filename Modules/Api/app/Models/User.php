<?php

namespace Modules\Api\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Api\Notifications\VerifyEmail;

class User extends \App\Models\User
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Relations
     */

    public function socialiteProviders(): HasMany
    {
        return $this->hasMany(SocialiteProvider::class);
    }
}
