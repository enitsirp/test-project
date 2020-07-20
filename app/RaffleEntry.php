<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RaffleEntry extends Model
{
    protected $table = 'raffle_entries';

    protected $fillable = [
        'raffle_name', 'is_selected', 
    ];

    public function scopeSelected($query, $is_selected)
    {
       return $query->where('is_selected', $is_selected);
    }
}
