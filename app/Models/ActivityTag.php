<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityTag extends Model
{
    protected $fillable = [ 
        'activity_id',
        'activity_tag_id'
     ];
}
