<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;

new #[Layout('layouts.products')] class extends Component
{
    public ?Cart $cart = null;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    // Load or create cart for user or guest
    public function loadCart()
    {
        if (Auth::check()) {
            $this->cart = Cart::firstOrCreate(
                ['user_id' => Auth::id(), 'status' => 'active']
            );
        } else {
            $sessionId = Session::get('cart_session_id', (string) Str::uuid());
            Session::put('cart_session_id', $sessionId);

            $this->cart = Cart::firstOrCreate(
                ['session_id' => $sessionId, 'status' => 'active']
            );
        }

        // Eager-load items + variants
        if ($this->cart) {
            $this->cart->load('items.variant');
        }
    }

    // Remove item securely
    public function removeItem($itemId)
    {
        if (!$this->cart) return;

        $item = $this->cart->items()->where('id', $itemId)->first();

        if ($item) {
            $item->delete();
            session()->flash('success', 'Item removed from cart.');
            $this->loadCart();
        }
    }

    // Compute total price for this cart
    public function getTotalProperty()
    {
        if (!$this->cart) return 0;

        return $this->cart->items->sum(function ($item) {
            return $item->variant->price * $item->quantity;
        });
    }

}
?>

<div class="max-w-5xl mx-auto space-y-6 p-4">

    @if(session()->has('success'))
        <p class="text-green-600">{{ session('success') }}</p>
    @endif

    @if($cart && $cart->items->isNotEmpty())
        <div class="grid grid-cols-1 gap-4">

            @foreach($cart->items as $item)
                <div class="flex items-center bg-white shadow rounded p-4">
                    
                    <img src="{{ $item->variant->image_url ?? 'https://via.placeholder.com/100x100?text=No+Image' }}" 
                         class="w-24 h-24 object-cover rounded mr-4">
                    
                    <div class="flex-1">
                        <h3 class="font-semibold">{{ $item->variant->name }}</h3>
                        <p class="text-yellow-600 font-bold mt-1">${{ number_format($item->variant->price, 2) }}</p>
                        <p class="mt-1">Quantity: {{ $item->quantity }}</p>
                    </div>

                    <button wire:click="removeItem({{ $item->id }})"
                            class="bg-red-500 hover:bg-red-400 text-white px-3 py-1 rounded">
                        Remove
                    </button>
                </div>
            @endforeach

        </div>

        <div class="mt-6 p-4 bg-gray-100 rounded flex justify-between items-center">
            <span class="font-semibold text-xl">Total: ${{ number_format($this->total, 2) }}</span>
            <a href="{{ route('checkout') }}" 
               class="bg-yellow-500 hover:bg-yellow-400 text-black px-6 py-2 rounded font-semibold">
                Checkout
            </a>
        </div>

    @else
        <p class="text-gray-500 text-center">Your cart is empty.</p>
    @endif

</div>