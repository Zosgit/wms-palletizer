<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ProductType;
use App\Models\ProductMetric;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['code', 'longdesc', 'producttype_id', 'size_x',
                        'size_y', 'size_z', 'weight', 'ean', 'metric_id',
                        'shipment', 'delivery', 'created_by'];


    public function producttype(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'producttype_id', 'id');
    }

    public function productmetric(): BelongsTo
    {
        return $this->belongsTo(ProductMetric::class, 'metric_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public static function getShipment()
    {
        return static::where('shipment', 0)->orderby('code','asc')->get();
    }

    public static function getOrder()
    {
        return static::where('delivery', 0)->orderby('code','asc')->get();
    }


    public static function booted(){

        static::creating(function($model)
        {
            $model->created_by = auth()->id();
        });
    }
}
