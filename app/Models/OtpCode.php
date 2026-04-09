<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;

    protected $table = 'otp_codes';

    protected $fillable = [
        'order_id',
        'contact_type',   // 'email' or 'whatsapp'
        'contact_value',  // email address or whatsapp number
        'otp',
        'expires_at',
    ];

    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function order()
{
    return $this->belongsTo(Order::class);
}
}