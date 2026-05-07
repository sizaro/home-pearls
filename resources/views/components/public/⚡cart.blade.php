<?php

namespace App\Http\Livewire;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use App\Models\Cart;

new #[Layout('layouts.products', title:'Cart')] class extends Component
{
    public ?Cart $cart = null;

    protected $listeners = ['cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        if (Auth::check()) {

            $this->cart = Cart::firstOrCreate([
                'user_id' => Auth::id(),
                'status' => 'active'
            ]);

        } else {

            $guestCartId = Cookie::get('guest_cart_id');

            if (!$guestCartId) {
                $guestCartId = (string) Str::uuid();
                Cookie::queue('guest_cart_id', $guestCartId, 60 * 24 * 30);
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

    public function removeItem($itemId)
    {
        if (!$this->cart) return;

        $item = $this->cart->items()->where('id', $itemId)->first();

        if ($item) {
            $item->delete();

            session()->flash('success', 'Item removed from cart.');
            $this->emit('cartUpdated');
            $this->loadCart();
        }
    }

    public function increase($itemId)
    {
        $item = $this->cart->items()->where('id', $itemId)->first();

        if ($item) {
            $item->increment('quantity');
            $this->emit('cartUpdated');
            $this->loadCart();
        }
    }

    public function decrease($itemId)
    {
        $item = $this->cart->items()->where('id', $itemId)->first();

        if ($item && $item->quantity > 1) {
            $item->decrement('quantity');
            $this->emit('cartUpdated');
            $this->loadCart();
        }
    }

    public function getTotalProperty()
    {
        if (!$this->cart) return 0;

        return $this->cart->items->sum(function ($item) {
            return $item->variant->price * $item->quantity;
        });
    }
};
?>

{{-- ================= UI ================= --}}
<div class="max-w-5xl mx-auto space-y-6 p-4 bg-[#F7F3EE] min-h-screen">

    @if(session()->has('success'))
        <p class="text-green-700 font-medium">
            {{ session('success') }}
        </p>
    @endif

    @if($cart && $cart->items->isNotEmpty())

        <div class="grid grid-cols-1 gap-4">

            @foreach($cart->items as $item)

                <div class="flex items-center gap-4 bg-white border border-[#8B5E3C]/20 shadow-sm rounded-xl p-4 hover:shadow-md transition">

                    <img
                        src="{{ $item->variant->image_url 
                            ? route('product-variants.image', ['id' => $item->variant->id])
                            : 'https://via.placeholder.com/300x200?text=No+Image' }}"
                        class="w-28 h-24 object-cover rounded-md border border-[#8B5E3C]/10"
                    >

                    <div class="flex-1">
                        <h3 class="font-semibold text-[#2B2B2B]">
                            {{ $item->variant->name }}
                        </h3>

                        <p class="text-[#8B5E3C] font-bold mt-1">
                            ${{ number_format($item->variant->price, 2) }}
                        </p>

                        {{-- Quantity --}}
                        <div class="flex items-center mt-3 gap-2">

                            <button
                                wire:click="decrease({{ $item->id }})"
                                class="w-8 h-8 bg-[#F7F3EE] border border-[#8B5E3C]/20 text-[#8B5E3C] rounded hover:bg-[#8B5E3C] hover:text-white transition"
                            >
                                -
                            </button>

                            <span class="text-[#2B2B2B] font-medium">
                                {{ $item->quantity }}
                            </span>

                            <button
                                wire:click="increase({{ $item->id }})"
                                class="w-8 h-8 bg-[#F7F3EE] border border-[#8B5E3C]/20 text-[#8B5E3C] rounded hover:bg-[#8B5E3C] hover:text-white transition"
                            >
                                +
                            </button>

                        </div>
                    </div>

                    <button
                        wire:click="removeItem({{ $item->id }})"
                        class="bg-[#38BDF8] hover:bg-[#1FA8D8] text-white px-4 py-2 rounded-md font-medium transition"
                    >
                        Remove
                    </button>

                </div>

            @endforeach

        </div>

        {{-- TOTAL --}}
        <div class="mt-6 p-5 bg-white border border-[#8B5E3C]/20 rounded-xl flex justify-between items-center">

            <span class="font-bold text-lg text-[#2B2B2B]">
                Total: ${{ number_format($this->total, 2) }}
            </span>

            <a href="{{ route('checkout') }}"
               class="bg-[#8B5E3C] hover:bg-[#6F472E] text-white px-6 py-2 rounded-md font-semibold transition">
                Checkout
            </a>

        </div>

    @else

        <p class="text-center text-[#8B5E3C]">
            Your cart is empty.
        </p>

    @endif

</div>