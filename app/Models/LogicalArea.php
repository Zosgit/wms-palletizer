<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogicalArea extends Model
{
    use HasFactory;

    protected $table = 'logical_areas';
    protected $fillable = ['code','longdesc'];

}
