<?php

namespace App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\Cart;

new #[Layout('layouts.products')] class extends Component
{
    public ?Cart $cart = null;

    public string $email = '';
    public string $whatsapp = '';

    public bool $otpSent = false;
    public string $otpInput = '';
    public string $generatedOtp = '';
    public bool $otpVerified = false;

    public function mount()
    {
        $this->loadCart();
    }

    // 🔥 SAME CART LOGIC (reuse from CartComponent)
    public function loadCart()
    {
        if (Auth::check()) {

            $this->cart = Cart::where('user_id', Auth::id())
                ->where('status', 'active')
                ->with('items.variant')
                ->first();

        } else {

            $guestCartId = Cookie::get('guest_cart_id');

            if ($guestCartId) {
                $this->cart = Cart::where('guest_cart_id', $guestCartId)
                    ->where('status', 'active')
                    ->with('items.variant')
                    ->first();
            }
        }
    }

    // 🔥 TOTAL
    public function getTotalProperty()
    {
        if (!$this->cart) return 0;

        return $this->cart->items->sum(function ($item) {
            return $item->variant->price * $item->quantity;
        });
    }

    // 🔐 SEND OTP (mock)
    public function sendOtp()
    {
        $this->validate([
            'email' => 'required|email',
            'whatsapp' => 'required|min:10'
        ]);

        $this->generatedOtp = '123456'; // mock
        $this->otpSent = true;

        session()->flash('success', 'OTP sent (use 123456)');
    }

    // 🔐 VERIFY OTP
    public function verifyOtp()
    {
        if ($this->otpInput === $this->generatedOtp) {
            $this->otpVerified = true;
            session()->flash('success', 'OTP verified');
        } else {
            session()->flash('error', 'Invalid OTP');
        }
    }

    // 🔥 PLACE ORDER (CORE LOGIC)
    public function placeOrder()
    {
        if (!$this->otpVerified) {
            session()->flash('error', 'Verify OTP first');
            return;
        }

        if (!$this->cart || $this->cart->items->isEmpty()) {
            session()->flash('error', 'Cart is empty');
            return;
        }

        // 🔥 CALCULATE TOTAL
        $total = $this->total;

        // 🔥 CREATE ORDER (will work after migrations)
        $order = \App\Models\Order::create([
            'user_id' => Auth::id(),
            'guest_cart_id' => $this->cart->guest_cart_id,
            'email' => $this->email,
            'whatsapp' => $this->whatsapp,
            'status' => 'pending',
            'total_amount' => $total,
        ]);

        // 🔥 MOVE ITEMS → ORDER ITEMS
        foreach ($this->cart->items as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'price' => $item->variant->price,
            ]);
        }

        // 🔥 CLOSE CART
        $this->cart->update([
            'status' => 'completed'
        ]);

        $this->cart->items()->delete();

        // 🔥 UPDATE HEADER
        $this->emit('cartUpdated');

        // 🔥 PASS DATA
        session()->put('order_total', $total);
        session()->put('order_id', $order->id);

        return redirect()->route('order.success');
    }
};
?>
<div class="max-w-4xl mx-auto p-4 space-y-6">

    <h1 class="text-2xl font-bold">Checkout</h1>

    {{-- CART SUMMARY --}}
    @if($cart && $cart->items->isNotEmpty())
        <div class="bg-white shadow rounded p-4 space-y-4">

            @foreach($cart->items as $item)
                <div class="flex items-center gap-4">
                    <img src="{{ $item->variant->image_url ?? 'https://via.placeholder.com/80' }}"
                         class="w-16 h-16 rounded">

                    <div class="flex-1">
                        <p>{{ $item->variant->name }}</p>
                        <p class="text-yellow-600">
                            ${{ number_format($item->variant->price, 2) }}
                        </p>
                    </div>

                    <p>Qty: {{ $item->quantity }}</p>
                </div>
            @endforeach

            <div class="text-right font-bold text-xl">
                Total: ${{ number_format($this->total, 2) }}
            </div>

        </div>

        {{-- CONTACT + OTP --}}
        <div class="bg-white shadow rounded p-4 space-y-4">

            <input type="email" wire:model="email" placeholder="Email"
                   class="w-full border px-3 py-2">

            <input type="text" wire:model="whatsapp" placeholder="WhatsApp Number"
                   class="w-full border px-3 py-2">

            @if(!$otpSent)
                <button wire:click="sendOtp"
                        class="bg-yellow-500 px-4 py-2 rounded">
                    Send OTP
                </button>
            @endif

            @if($otpSent && !$otpVerified)
                <input type="text" wire:model="otpInput" placeholder="Enter OTP"
                       class="w-full border px-3 py-2">

                <button wire:click="verifyOtp"
                        class="bg-green-500 text-white px-4 py-2 rounded">
                    Verify OTP
                </button>
            @endif

            @if($otpVerified)
                <button wire:click="placeOrder"
                        class="bg-black text-white px-6 py-2 rounded w-full">
                    Place Order
                </button>
            @endif

        </div>

    @else
        <p class="text-gray-500 text-center">Cart is empty.</p>
    @endif

    {{-- FLASH --}}
    @if(session()->has('success'))
        <p class="text-green-600">{{ session('success') }}</p>
    @endif

    @if(session()->has('error'))
        <p class="text-red-600">{{ session('error') }}</p>
    @endif

</div>