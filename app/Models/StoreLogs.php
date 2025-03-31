<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreLogs extends Model
{
    use HasFactory;

    protected $table = 'store_logs';
    protected $fillable = ['job_type','storeunit_id_in','storeunit_ean_in','storeunit_id_out','storeunit_ean_out',
                            'location_in','location_ean_in','location_out','location_ean_out','prod_id','prod_code','prod_desc','expiration_at',
                            'quantity','serial_nr','ship_detail_id','notes',
                            'logical_area_id_in','logical_area_code_in','logical_area_id_out','logical_area_code_out'];


    public static function booted(){

        static::creating(function($model)
        {
            $model->created_by = auth()->id();
        });
    }

}
/*
    job_type
    1 - operacje na lokacji
    2 - operacje na opakowaniu
    3 - operacje na produkt
    4 - operacje na mag logicznym

*/
