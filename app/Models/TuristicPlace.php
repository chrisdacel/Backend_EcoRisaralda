<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TuristicPlace extends Model
{
    use HasFactory;

   

    // Campos que se pueden asignar masivamente (fillable)
    protected $fillable = [
        'name',
        'slogan',
        'cover',
        'description',
        'localization',
        'lat',
        'lng',
        'Weather',
        'Weather_img',
        'features',
        'features_img',
        'flora',
        'flora_img',
        'estructure',
        'estructure_img',
        'tips',
        'contact_info',
        'open_days',
        'opening_status',
        'user_id',
        'terminos',
        'politicas',
        'archived_at',
        'approval_status',
    ];

    protected $casts = [
        'open_days' => 'array',
    ];

    // Relación con usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoriteby(){
        return $this->belongsToMany(User::class, 'favorite_places', 'place_id', 'user_id')
            ->wherePivotNull('archived_at')
            ->withTimestamps();

    }
    public function label(){
        return $this->belongsToMany(preference::class,'label_place', 'place_id', 'label_id')
            ->withTimestamps()
            ->using(LabelPlace::class);
    }

    public function events()
    {
        return $this->hasMany(PlaceEvent::class, 'place_id');
    }
}
