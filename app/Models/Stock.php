<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\LogicalArea;
use App\Models\StoreUnit;
use App\Models\Firm;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stocks';
    protected $fillable = ['store_unit_id','product_id','prod_code','prod_desc','expiration_at','serial_nr','quarantine','owner_id',
    'ship_detail_id','logical_area_id','quantity','fifo','remarks','status_id'];


    public static function getStockStoreUnit($id)
    {
        return static::where('store_unit_id',$id)->orderby('prod_code','asc')->get();
    }

    public function logical_area(): BelongsTo
    {
        return $this->belongsTo(LogicalArea::class, 'logical_area_id', 'id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Firm::class, 'owner_id', 'id');
    }

    public function StoreUnit(): BelongsTo
    {
        return $this->belongsTo(StoreUnit::class, 'store_unit_id', 'id');
    }

    public static function booted(){

        static::creating(function($model)
        {
            $model->created_by = auth()->id();
        });
    }
}
