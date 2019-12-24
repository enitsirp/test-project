<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
     protected $fillable = [
        'title', 'note_details' , 'created_by'
    ];


    public function creator()
    {
        return $this->belongsTo('User', 'created_by');
    }
}
