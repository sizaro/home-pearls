<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_cart_id',
        'email',
        'whatsapp',
        'status',
        'total_amount',
    ];

    // 🔹 Belongs to user (nullable)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔹 Has many order items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}