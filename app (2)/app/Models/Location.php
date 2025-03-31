<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\StoreArea;
use App\Models\Status;

class Location extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['storearea_id','ean','pos_x','pos_y','pos_z',
                            'size_x','size_y','size_z','loadwgt',
                            'status_id','created_by'];

    public function getStoreAreaCount($store_areas, $status)
    {
        return $this->where('storearea_id', $store_areas)->where('status_id', $status)->count();
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
