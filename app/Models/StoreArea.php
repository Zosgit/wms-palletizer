<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreArea extends Model
{
    use HasFactory;

    protected $table = 'store_areas';
    protected $fillable = ['code','longdesc'];



}
