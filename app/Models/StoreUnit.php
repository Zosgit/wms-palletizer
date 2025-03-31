<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Counter;
use App\Models\StoreUnitType;
use App\Models\Location;
use App\Models\Status;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreUnit extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['ean', 'storeunittype_id', 'location_id','su_multi','status_id','print'];

    public static function getInLocation()
    {
        return static::whereNotNull('location_id')->count();
    }

    public static function getLocationStoreUnit($su)
    {
        return static::where('location_id', $su)->count();
    }

    public static function getUnitShipment()
    {
        return static::where('status_id', 101)->paginate(1000);
    }

    // tylko zajęte miejsca
    public static function getMoved()
    {
        return static::where('status_id', 103)->get();
    }

    public static function getPrintLabel()
    {
        return static::whereNull('location_id')->count();
    }

    public function storeunittype(): BelongsTo
    {
        return $this->belongsTo(StoreUnitType::class, 'storeunittype_id', 'id');
    }

    public function sulocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function sustatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public static function booted()
    {
        static::creating(function($model)
        {
            $model->created_by = auth()->id();
            $model->status_id = 101;
        });
    }

}


// $ok = $this->generate_sscc_barcode(127,0,5905468,127);
