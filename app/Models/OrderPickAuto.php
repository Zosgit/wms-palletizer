<?php

namespace App\Models;
use App\Models\Order;
use App\Models\StoreUnit;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPickAuto extends Model
{
    protected $fillable = [
        'order_id',
        'store_unit_id',
        'used_volume',
        'used_weight',
        'confirmed',
        'algorithm_type',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function storeUnit()
    {
        return $this->belongsTo(StoreUnit::class);
    }

    public function products()
{
    return $this->belongsToMany(Product::class, 'order_pick_auto_product', 'order_pick_auto_id', 'product_id');
}

public $timestamps = true;
}
