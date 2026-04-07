<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LabelPlace extends Pivot
{
    protected $fillable = ['label_id', 'place_id'];
    protected $table = 'label_place';
}
