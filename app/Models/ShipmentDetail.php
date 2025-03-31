<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Shipment;
use App\Models\Status;
use App\Models\User;
use App\Models\LogicalArea;
use App\Models\Product;


class ShipmentDetail extends Model
{
    use HasFactory;

    protected $table = 'shipment_details';
    protected $fillable = ['ship_id','product_id','prod_code',
                            'prod_desc','serial_nr','expiration_at',
                            'logical_area_id','quantity','remarks','status_id','quantity_control'];


    // czy cała dostawa skontrolowana
    public static function getControls($id)
    {
        return static::where('ship_id', $id)->where('quantity_control','>',0)->count();
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'ship_id', 'id');
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
            $model->status_id = 401;
            $model->quantity_control = $model->quantity;
        });
    }
}
