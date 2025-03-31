<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\StoreArea;
use App\Models\Status;
use App\Models\StoreLogs;
use DB;

class Location extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['storearea_id','ean','pos_x','pos_y','pos_z',
                            'size_x','size_y','size_z','loadwgt',
                            'status_id','created_by'];

    public static function SetStatus($id,$number,$notes)
    {

        $temp = static::findOrFail($id);
        $ean = $temp->ean;
        $temp->status_id = $number;
        $temp->update();

        return StoreLogs::create([
                'job_type' => 1,
                'location_out' => $id,
                'location_ean_out' => $ean,
                'notes' => $notes
            ]);
    }

    public function getStoreAreaCount($store_areas, $status)
    {
        return $this->where('storearea_id', $store_areas)->where('status_id', $status)->count();
    }

    public static function getLocationShipment()
    {
        return static::where('status_id', 202)->where('storearea_id',1)->get();
    }

    public static function getLocationDelivery()
    {
        return static::where('status_id', 202)->where('storearea_id',2)->get();
    }

    // tylko zajęte miejsca
    public static function getMoved()
    {
        return static::where('status_id', 204)->get();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function store_area(): BelongsTo
    {
        return $this->belongsTo(StoreArea::class, 'storearea_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public static function booted(){

        static::creating(function($model)
        {
            $model->created_by = auth()->id();
        });
    }
}
