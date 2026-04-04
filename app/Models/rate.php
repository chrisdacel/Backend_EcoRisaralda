<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
 protected $fillable = [

    'rating',
    'place_id' 


 ];

  public function place()
    {
        return $this->belongsTo(TuristicPlace::class, 'place_id');
    }
}
