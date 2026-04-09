<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
new #[Layout('layouts.products')] class extends Component
{
    //
};
?>

<div class="max-w-2xl mx-auto p-6 text-center space-y-6">

    {{-- SUCCESS ICON --}}
    <div class="text-6xl text-green-500">
        ✅
    </div>

    {{-- TITLE --}}
    <h1 class="text-3xl font-bold">
        Order Placed Successfully!
    </h1>

    {{-- MESSAGE --}}
    <p class="text-gray-600">
        Thank you for your order. We’ve received your request and will contact you shortly.
    </p>

    {{-- MOCK ORDER DETAILS --}}
    <div class="bg-white shadow rounded p-6 text-left space-y-3">

        <p><strong>Order Ref:</strong> #HP{{ rand(1000, 9999) }}</p>

        <p><strong>Total:</strong> ${{ session('order_total', '---') }}</p>

        <p><strong>Status:</strong> Pending Confirmation</p>

    </div>

    {{-- WHATSAPP INFO --}}
    <div class="bg-yellow-100 p-4 rounded text-sm">
        📲 We will contact you via WhatsApp to confirm your order and arrange delivery.
    </div>

    {{-- ACTIONS --}}
    <div class="flex justify-center gap-4">

        <a href="{{ route('home-pearls') }}"
           class="bg-black text-white px-6 py-2 rounded">
            Continue Shopping
        </a>

        <a href="{{ route('cart') }}"
           class="border px-6 py-2 rounded">
            View Cart
        </a>

    </div>

</div>