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
use Twilio\Rest\Client;

new #[Layout('layouts.products')] class extends Component
{
    public ?Cart $cart = null;

    public string $email = '';
    public string $whatsapp = '';
    public string $otpInput = '';
    public string $selectedVerification = ''; // 'email' or 'whatsapp'

    public bool $otpSent = false;
    public bool $otpVerified = false;

    public function mount()
    {
        $this->loadCart();
    }

    // 🔥 Load Cart (reuse logic)
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

    // 🔥 Total
    public function getTotalProperty()
    {
        if (!$this->cart) return 0;
        return $this->cart->items->sum(fn($item) => $item->variant->price * $item->quantity);
    }

    // 🔐 Send OTP
    public function sendOtp($method)
    {
        $this->validate([
            'email' => 'required|email',
            'whatsapp' => 'required|min:10'
        ]);

        $this->selectedVerification = $method;
        $otp = rand(100000, 999999);

        // Save OTP to DB
        OtpCode::updateOrCreate(
            [
                'contact_type' => $method,
                'contact_value' => $method === 'email' ? $this->email : $this->whatsapp,
            ],
            [
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]
        );

        // 🔹 Send OTP
        if ($method === 'email') {
            Mail::raw("Your OTP code: $otp", function ($message) {
                $message->to($this->email)
                        ->subject('Verify Your Email');
            });
        } else if ($method === 'whatsapp') {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $twilio_number = env('TWILIO_WHATSAPP_NUMBER');
            $client = new Client($sid, $token);
            $client->messages->create(
                "whatsapp:".$this->whatsapp,
                [
                    'from' => "whatsapp:$twilio_number",
                    'body' => "Your OTP code: $otp"
                ]
            );
        }

        $this->otpSent = true;
        session()->flash('success', "OTP sent via $method.");
    }

    // 🔐 Verify OTP
    public function verifyOtp()
    {
        $record = OtpCode::where('contact_type', $this->selectedVerification)
            ->where('contact_value', $this->selectedVerification === 'email' ? $this->email : $this->whatsapp)
            ->where('otp', $this->otpInput)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($record) {
            $this->otpVerified = true;
            $record->delete(); // remove OTP after successful verification
            session()->flash('success', 'OTP verified!');
        } else {
            session()->flash('error', 'Invalid or expired OTP.');
        }
    }

    // 🔥 Place Order
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
            'whatsapp' => $this->whatsapp,
            'status' => 'pending',
            'total_amount' => $total,
            'verified_contact_method' => $this->selectedVerification
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

        $this->emit('cartUpdated');

        session()->put('order_total', $total);
        session()->put('order_id', $order->id);

        return redirect()->route('order.success');
    }
}

?>

<div class="max-w-4xl mx-auto p-4 space-y-6">

    <h1 class="text-2xl font-bold">Checkout</h1>

    {{-- CART SUMMARY --}}
    @if($cart && $cart->items->isNotEmpty())
        <div class="bg-white shadow rounded p-4 space-y-4">
            @foreach($cart->items as $item)
                <div class="flex items-center gap-4">
                    <img
                        src="{{ $item->variant->image_url 
                            ? route('product-variants.image', ['id' => $item->variant->id])
                            : 'https://via.placeholder.com/300x200?text=No+Image' }}"
                        class="w-full h-28 object-cover rounded mb-2"
                    >
                    <div class="flex-1">
                        <p>{{ $item->variant->name }}</p>
                        <p class="text-yellow-600">${{ number_format($item->variant->price, 2) }}</p>
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
            <input type="email" wire:model="email" placeholder="Email" class="w-full border px-3 py-2">
            <input type="text" wire:model="whatsapp" placeholder="WhatsApp Number" class="w-full border px-3 py-2">

            @if(!$otpSent)
                <div class="flex gap-4">
                    <button wire:click="sendOtp('email')" class="bg-yellow-500 px-4 py-2 rounded">Verify Email</button>
                    <button wire:click="sendOtp('whatsapp')" class="bg-green-500 text-white px-4 py-2 rounded">Verify WhatsApp</button>
                </div>
            @endif

            @if($otpSent && !$otpVerified)
                <input type="text" wire:model="otpInput" placeholder="Enter OTP" class="w-full border px-3 py-2">
                <button wire:click="verifyOtp" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Verify OTP</button>
            @endif

            @if($otpVerified)
                <button wire:click="placeOrder" class="bg-black text-white px-6 py-2 rounded w-full">Place Order</button>
            @endif
        </div>
    @else
        <p class="text-gray-500 text-center">Cart is empty.</p>
    @endif

    {{-- FLASH --}}
    @if(session()->has('success')) <p class="text-green-600">{{ session('success') }}</p> @endif
    @if(session()->has('error')) <p class="text-red-600">{{ session('error') }}</p> @endif

</div>