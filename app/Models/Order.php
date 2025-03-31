<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Status;
use App\Models\Firm;
use App\Models\Location;

class Order extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['order_nr','external_nr','firm_id','owner_id','remarks','realization_at','progress',
        'status_id','created_by','location_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function firm(): BelongsTo
    {
        return $this->belongsTo(Firm::class, 'firm_id', 'id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Firm::class, 'owner_id', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }


    public static function booted(){

        static::creating(function($model)
        {
            $model->created_by = auth()->id();
            $model->status_id = 502;
            $model->realization_at = now();
        });
    }
}
