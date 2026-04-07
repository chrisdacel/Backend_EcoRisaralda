<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class preference extends Model
{
       protected $fillable = [
        'name','image','color'
        
        
      
    ];

    public function users()
{
    return $this->belongsToMany(user::class);
}
  public function places(){
    return $this->belongsToMany(TuristicPlace::class, 'label_place', 'label_id', 'place_id')
        ->withTimestamps()
        ->using(LabelPlace::class);
}
}