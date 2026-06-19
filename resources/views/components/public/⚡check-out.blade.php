<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OtpCode;

new #[Layout('layouts.products')] class extends Component
{
    public ?Cart $cart = null;

    public string $email = '';
    public string $whatsapp = '';
    public string $otpInput = '';
    public string $selectedVerification = '';

    public bool $otpSent = false;
    public bool $otpVerified = false;

    public function mount()
    {
        $this->loadCart();
    }

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

    public function getTotalProperty()
    {
        if (!$this->cart) return 0;

        return $this->cart->items->sum(
            fn($item) => $item->variant->price * $item->quantity
        );
    }

    // ✅ EMAIL ONLY OTP (WhatsApp disabled safely)
    public function sendOtp($method)
    {
        if ($method !== 'email') {
            session()->flash('error', 'WhatsApp verification coming soon.');
            return;
        }

        $this->validate([
            'email' => 'required|email',
        ]);

        $this->selectedVerification = 'email';
        $otp = rand(100000, 999999);

        OtpCode::updateOrCreate(
            [
                'contact_type' => 'email',
                'contact_value' => $this->email,
            ],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        Mail::raw("Your OTP code: $otp", function ($message) {
            $message->to($this->email)
                    ->subject('Verify Your Email');
        });

        $this->otpSent = true;
        session()->flash('success', 'OTP sent to your email.');
    }

    public function verifyOtp()
    {
        $record = OtpCode::where('contact_type', 'email')
            ->where('contact_value', $this->email)
            ->where('otp', $this->otpInput)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($record) {
            $this->otpVerified = true;
            $record->delete();

            session()->flash('success', 'OTP verified!');
        } else {
            session()->flash('error', 'Invalid or expired OTP.');
        }
    }

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

        $total = $this->total;

        $order = Order::create([
            'user_id' => Auth::id(),
            'guest_cart_id' => $this->cart->guest_cart_id,
            'email' => $this->email,
            'whatsapp' => $this->whatsapp, // ✔ stored for later use
            'status' => 'pending',
            'total_amount' => $total,
            'verified_contact_method' => 'email'
        ]);

        foreach ($this->cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'variant_id' => $item->variant_id,
                'quantity' => $item->quantity,
                'price' => $item->variant->price,
            ]);
        }

        $this->cart->update(['status' => 'completed']);
        $this->cart->items()->delete();

        $this->dispatch('cartUpdated');

        session()->put('order_total', $total);
        session()->put('order_id', $order->id);

        return redirect()->route('order.success');
    }
}

?>


<div class="min-h-screen bg-[#F6F1EB] text-[#3B2F2A] max-w-4xl mx-auto p-4 space-y-6">

    <h1 class="text-2xl font-bold">Checkout</h1>

    @if($cart && $cart->items->isNotEmpty())

        {{-- CART ITEMS --}}
        <div class="bg-[#E7DED5] shadow rounded-xl p-4 space-y-4">

            @foreach($cart->items as $item)
                <div class="flex items-center gap-4 border-b border-[#3B2F2A]/10 pb-3">

                    <img
                        src="{{ $item->variant->image_url 
                            ? route('product-variants.image', ['id' => $item->variant->id])
                            : 'https://via.placeholder.com/300x200' }}"
                        class="w-20 h-20 object-cover rounded"
                    >

                    <div class="flex-1">
                        <p class="font-medium">{{ $item->variant->name }}</p>
                        <p class="text-[#38BDF8] font-semibold">
                            ${{ number_format($item->variant->price, 2) }}
                        </p>
                    </div>

                    <p class="text-sm">Qty: {{ $item->quantity }}</p>

                </div>
            @endforeach

            <div class="text-right font-bold text-xl pt-2">
                Total: ${{ number_format($this->total, 2) }}
            </div>

        </div>

        {{-- VERIFICATION FORM --}}
        <div class="bg-[#E7DED5] shadow rounded-xl p-4 space-y-4">

            <input type="email"
                wire:model="email"
                placeholder="Email"
                class="w-full border border-[#3B2F2A]/20 px-3 py-2 rounded bg-[#F6F1EB] text-[#3B2F2A]"
            >

            <input type="text"
                wire:model="whatsapp"
                placeholder="WhatsApp Number"
                class="w-full border border-[#3B2F2A]/20 px-3 py-2 rounded bg-[#F6F1EB] text-[#3B2F2A]"
            >

            @if(!$otpSent)
                <button wire:click="sendOtp('email')"
                    class="bg-[#38BDF8] text-white px-4 py-2 rounded w-full font-semibold">
                    Verify Email
                </button>

                <p class="text-sm text-[#3B2F2A]/60 text-center">
                    WhatsApp verification coming soon
                </p>
            @endif

            @if($otpSent && !$otpVerified)
                <input type="text"
                    wire:model="otpInput"
                    placeholder="Enter OTP"
                    class="w-full border border-[#3B2F2A]/20 px-3 py-2 rounded bg-[#F6F1EB] text-[#3B2F2A]"
                >

                <button wire:click="verifyOtp"
                    class="bg-[#38BDF8] text-white px-4 py-2 rounded w-full font-semibold">
                    Verify OTP
                </button>
            @endif

            @if($otpVerified)
                <button wire:click="placeOrder"
                    class="bg-[#3B2F2A] text-white px-6 py-2 rounded w-full font-semibold">
                    Place Order
                </button>
            @endif

        </div>

    @else
        <p class="text-[#3B2F2A]/60 text-center">Cart is empty.</p>
    @endif

    @if(session()->has('success'))
        <p class="text-green-600">{{ session('success') }}</p>
    @endif

    @if(session()->has('error'))
        <p class="text-red-600">{{ session('error') }}</p>
    @endif

</div>