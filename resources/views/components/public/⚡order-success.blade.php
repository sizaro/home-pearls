<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
new #[Layout('layouts.products')] class extends Component
{
    //
};
?>




<div class="min-h-screen bg-[#F6F1EB] text-[#3B2F2A] flex items-center justify-center p-6">

    <div class="max-w-2xl w-full text-center space-y-6">

        {{-- SUCCESS ICON --}}
        <div class="text-6xl text-[#38BDF8]">
            ✅
        </div>

        {{-- TITLE --}}
        <h1 class="text-3xl font-bold">
            Order Placed Successfully!
        </h1>

        {{-- MESSAGE --}}
        <p class="text-[#3B2F2A]/70">
            Thank you for your order. We’ve received your request and will contact you shortly.
        </p>

        {{-- ORDER DETAILS --}}
        <div class="bg-[#E7DED5] shadow rounded-xl p-6 text-left space-y-3 border border-[#3B2F2A]/10">

            <p><strong>Order Ref:</strong> #HP{{ rand(1000, 9999) }}</p>

            <p>
                <strong>Total:</strong>
                <span class="text-[#38BDF8] font-semibold">
                    ${{ session('order_total', '---') }}
                </span>
            </p>

            <p><strong>Status:</strong> Pending Confirmation</p>

        </div>

        {{-- INFO BOX --}}
        <div class="bg-[#E7DED5] border border-[#38BDF8]/30 p-4 rounded-xl text-sm text-[#3B2F2A]/80">

            📲 We will contact you via WhatsApp to confirm your order and arrange delivery.

        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-center gap-4">

            <a href="{{ route('home-pearls') }}"
               class="bg-[#38BDF8] text-white px-6 py-2 rounded-lg font-semibold hover:opacity-90 transition shadow-sm">
                Continue Shopping
            </a>

            <a href="{{ route('cart') }}"
               class="border border-[#3B2F2A]/20 px-6 py-2 rounded-lg hover:bg-[#E7DED5] transition">
                View Cart
            </a>

        </div>

    </div>

</div>