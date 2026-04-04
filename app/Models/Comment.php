<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'comment'
    ];


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
