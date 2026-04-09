<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'variant_id',
        'quantity',
        'price',
    ];

    // 🔹 Belongs to order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 🔹 Belongs to ProductVariant
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}