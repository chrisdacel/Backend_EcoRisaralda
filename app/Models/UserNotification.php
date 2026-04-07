<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'preview',
        'target_type',
        'target_id',
        'place_id',
        'place_name',
        'read_at',
        'archived_at',
    ];

    protected $dates = [
        'read_at',
        'archived_at',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
