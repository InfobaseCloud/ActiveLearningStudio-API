<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class H5pRecords extends Model
{
    protected $fillable = [
        'playlist_id',
        'activity_id',
        'statement',
        'activity_name'
    ];
}