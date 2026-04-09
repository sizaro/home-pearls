<?php

namespace App\Http\Livewire;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use App\Models\Cart;

new #[Layout('layouts.products')] class extends Component
{
    public ?Cart $cart = null;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    // 🔥 Load cart (USER or GUEST via COOKIE)
    public function loadCart()
    {
        if (Auth::check()) {

            $this->cart = Cart::firstOrCreate([
                'user_id' => Auth::id(),
                'status' => 'active'
            ]);

        } else {

            // ✅ COOKIE instead of session
            $guestCartId = Cookie::get('guest_cart_id');

            if (!$guestCartId) {
                $guestCartId = (string) Str::uuid();

                Cookie::queue('guest_cart_id', $guestCartId, 60 * 24 * 30); // 30 days
            }

            $this->cart = Cart::firstOrCreate([
                'guest_cart_id' => $guestCartId,
                'status' => 'active'
            ]);
        }

        if ($this->cart) {
            $this->cart->load('items.variant');
        }
    }

    // 🔥 REMOVE ITEM (SAFE)
    public function removeItem($itemId)
    {
        if (!$this->cart) return;

        $item = $this->cart->items()
            ->where('id', $itemId)
            ->first();

        if ($item) {
            $item->delete();

            session()->flash('success', 'Item removed from cart.');

            $this->emit('cartUpdated'); // 🔥 update header
            $this->loadCart();
        }
    }

    // 🔥 OPTIONAL (PRO LEVEL): Increase Quantity
    public function increase($itemId)
    {
        $item = $this->cart->items()->where('id', $itemId)->first();

        if ($item) {
            $item->increment('quantity');
            $this->emit('cartUpdated');
            $this->loadCart();
        }
    }

    // 🔥 OPTIONAL: Decrease Quantity
    public function decrease($itemId)
    {
        $item = $this->cart->items()->where('id', $itemId)->first();

        if ($item && $item->quantity > 1) {
            $item->decrement('quantity');
            $this->emit('cartUpdated');
            $this->loadCart();
        }
    }

    // 🔥 TOTAL (SAFE)
    public function getTotalProperty()
    {
        if (!$this->cart) return 0;

        return $this->cart->items->sum(function ($item) {
            return $item->variant->price * $item->quantity;
        });
    }
};
?>

<div class="max-w-5xl mx-auto space-y-6 p-4">

    @if(session()->has('success'))
        <p class="text-green-600">{{ session('success') }}</p>
    @endif

    @if($cart && $cart->items->isNotEmpty())
        <div class="grid grid-cols-1 gap-4">

            @foreach($cart->items as $item)
                <div class="flex items-center bg-white shadow rounded p-4">
                    
                     <img
                        src="{{ $item->variant->image_url 
                            ? route('product-variants.image', ['id' => $item->variant->id])
                            : 'https://via.placeholder.com/300x200?text=No+Image' }}"
                        class="w-full h-28 object-cover rounded mb-2"
                    >
                    
                    <div class="flex-1">
                        <h3 class="font-semibold">{{ $item->variant->name }}</h3>
                        <p class="text-yellow-600 font-bold mt-1">
                            ${{ number_format($item->variant->price, 2) }}
                        </p>

                        {{-- 🔥 Quantity Controls --}}
                        <div class="flex items-center mt-2 gap-2">
                            <button wire:click="decrease({{ $item->id }})"
                                class="px-2 bg-gray-200">-</button>

                            <span>{{ $item->quantity }}</span>

                            <button wire:click="increase({{ $item->id }})"
                                class="px-2 bg-gray-200">+</button>
                        </div>
                    </div>

                    <button wire:click="removeItem({{ $item->id }})"
                        class="bg-red-500 hover:bg-red-400 text-white px-3 py-1 rounded">
                        Remove
                    </button>
                </div>
            @endforeach

        </div>

        <div class="mt-6 p-4 bg-gray-100 rounded flex justify-between items-center">
            <span class="font-semibold text-xl">
                Total: ${{ number_format($this->total, 2) }}
            </span>

            <a href="{{ route('checkout') }}" 
               class="bg-yellow-500 hover:bg-yellow-400 text-black px-6 py-2 rounded font-semibold">
                Checkout
            </a>
        </div>

    @else
        <p class="text-gray-500 text-center">Your cart is empty.</p>
    @endif

</div>