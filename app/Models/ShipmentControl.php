<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentControl extends Model
{
    use HasFactory;

    protected $table = 'shipment_control';
    protected $fillable = ['ship_id','store_unit_id','store_unit_ean','product_id','prod_code','prod_desc','expiration_at','serial_nr','quarantine',
    'ship_detail_id','logical_area_id','quantity','fifo','remarks','status_id'];

    public static function booted(){

        static::creating(function($model)
        {
            $model->created_by = auth()->id();
        });
    }
}
