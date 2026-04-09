<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->string('guest_cart_id')->nullable();
    $table->string('email');
    $table->string('whatsapp');
    $table->enum('status', ['pending','processing','completed','delivered','paid'])->default('pending');
    $table->decimal('total_amount', 10, 2);
    $table->string('verified_contact_method')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};