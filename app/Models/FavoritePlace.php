<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoritePlace extends Model
{
    protected $fillable = ['user_id', 'place_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function place()
    {
        return $this->belongsTo(TuristicPlace::class, 'place_id');
    }
}
