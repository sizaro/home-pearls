<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'description',
        'price',
        'stock',
        'sku',
        'image_url',
    ];

    /**
     * A variant belongs to a product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}