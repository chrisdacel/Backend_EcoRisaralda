<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'type'
    ];

    public function review()
    {
        return $this->belongsTo(reviews::class, 'review_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
