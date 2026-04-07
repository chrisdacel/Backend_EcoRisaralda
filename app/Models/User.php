<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'country_id',
        'date_of_birth',
        'password',
        'image',
        'role',
        'status',
        'first_time_preferences'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function preferences()
    {
        return $this->belongsToMany(preference::class)
            ->wherePivotNull('archived_at');
    }
    public function favoritePlaces(){
        return $this->belongsToMany(TuristicPlace::class, 'favorite_places', 'user_id','place_id')
            ->wherePivotNull('archived_at')
            ->whereNull('turistic_places.archived_at')
            ->withTimestamps();
    }

}
