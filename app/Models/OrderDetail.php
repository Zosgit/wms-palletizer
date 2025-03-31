<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Order;
use App\Models\Status;
use App\Models\User;
use App\Models\LogicalArea;
use App\Models\Product;


class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = ['order_id','product_id','prod_code',
                            'prod_desc','serial_nr','expiration_at',
                            'logical_area_id','quantity','quantity_pick','remarks','status_id'];

    public static function getfull($id)
    {
        return static::where('order_id', $id)->where('quantity_pick','>',0)->count();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function logical_area(): BelongsTo
    {
        return $this->belongsTo(LogicalArea::class, 'logical_area_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public static function booted(){

        static::creating(function($model)
        {
            $model->created_by = auth()->id();
            $model->status_id = 501;
            $model->quantity_pick = $model->quantity;
        });
    }
}
