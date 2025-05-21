<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSet extends Model
{
    use HasFactory;

    protected $fillable = ['code'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'complete_product', 'product_sets_id', 'product_id');
    }

}
