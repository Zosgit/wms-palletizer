<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogLocations extends Model
{
    use HasFactory;

    protected $table = 'log_locations';
    protected $fillable = ['location_id','status_id','notes'];

    public static function booted(){

        static::creating(function($model)
        {
            $model->created_by = auth()->id();
        });
    }

}
