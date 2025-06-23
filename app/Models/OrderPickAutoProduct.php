<?php

namespace App\Models;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPickAutoProduct extends Model
{
    use HasFactory;

    protected $table = 'order_pick_auto_product'; // dokładna nazwa tabeli

    protected $fillable = [
        'order_pick_auto_id',
        'product_id',
        'quantity',
    ];

    public $timestamps = true; // masz kolumnę created_at

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
