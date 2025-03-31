<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\LogicalArea;

class PickOrder extends Model
{
    use HasFactory;

    protected $table = 'order_pick';
    protected $fillable = ['id','order_id','store_unit_id','store_unit_ean','product_id','prod_code','prod_desc','expiration_at','serial_nr','quarantine',
    'ship_detail_id','logical_area_id','quantity','fifo','remarks','status_id'];

    public static function getActiveStoreUnit($order_id)
    {
        return static::where('order_id', $order_id)->where('status_id',102)->first();
    }

    public function logical_area(): BelongsTo
    {
        return $this->belongsTo(LogicalArea::class, 'logical_area_id', 'id');
    }

    public static function booted(){

        static::creating(function($model)
        {
            $model->created_by = auth()->id();
        });
    }
}
