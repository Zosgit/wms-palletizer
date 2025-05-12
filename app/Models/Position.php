<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'storeunit_id', 'order_id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'position_product');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
