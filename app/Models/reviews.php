<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reviews extends Model
{
    protected $fillable = [
        'rating',
        'comment',
        'user_id',
        'place_id',
        'is_restricted',
        'restricted_by_role',
        'restriction_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function place()
    {
        return $this->belongsTo(TuristicPlace::class, 'place_id');
    }
    
    public function reactions()
    {
        return $this->hasMany(ReviewReaction::class, 'review_id')
            ->whereNull('archived_at');
    }
    
    public function likes()
    {
        return $this->reactions()->where('type', 'like');
    }
    
    public function dislikes()
    {
        return $this->reactions()->where('type', 'dislike');
    }
}
