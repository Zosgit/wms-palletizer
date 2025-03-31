<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Firm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['code', 'longdesc', 'tax', 'street',
                         'postcode', 'city', 'notes', 'shipment',
                         'owner', 'delivery', 'created_by'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public static function getShipment()
    {
        return static::where('shipment', 1)->orderby('code','asc')->get();
    }

    public static function getOwner()
    {
        return static::where('owner', 1)->orderby('code','asc')->get();
    }

    public static function getDelivery()
    {
        return static::where('delivery', 1)->orderby('code','asc')->get();
    }

    public static function booted(){

        static::creating(function($model)
        {
            $model->created_by = auth()->id();
        });
    }
}
