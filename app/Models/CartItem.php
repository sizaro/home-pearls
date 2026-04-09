<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'variant_id',
        'quantity',
    ];

    // 🔹 Belongs to Cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // 🔹 Belongs to ProductVariant
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}