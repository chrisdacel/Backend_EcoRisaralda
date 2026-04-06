<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'title',
        'description',
        'image',
        'starts_at',
        'ends_at',
        'approval_status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function place()
    {
        return $this->belongsTo(TuristicPlace::class, 'place_id');
    }
}
