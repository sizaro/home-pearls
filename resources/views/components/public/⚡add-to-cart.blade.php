<?php

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;

new class extends Component
{
    public int $variantId;
    public int $quantity = 1;
    public ?Cart $cart = null;

    public function mount($variantId, $quantity = 1)
    {
        $this->variantId = $variantId;
        $this->quantity = $quantity;

        $this->initializeCart();
    }

    private function initializeCart()
{
    if (Auth::check()) {
        $this->cart = Cart::firstOrCreate([
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);
        return;
    }

    $guestCartId = Cookie::get('guest_cart_id');

    if ($guestCartId) {
        $existingCart = Cart::where('guest_cart_id', $guestCartId)->first();

        // ✅ If cart exists AND is still active → reuse
        if ($existingCart && $existingCart->status === 'active') {
            $this->cart = $existingCart;
            return;
        }
    }

    // 🔥 Otherwise create NEW cart + NEW ID
    $newCartId = (string) Str::uuid();

    Cookie::queue('guest_cart_id', $newCartId, 60 * 24 * 30);

    $this->cart = Cart::create([
        'guest_cart_id' => $newCartId,
        'status' => 'active',
    ]);
}

    public function add()
    {
        $variant = ProductVariant::findOrFail($this->variantId);

        $item = $this->cart->items()->firstOrCreate(
            ['variant_id' => $variant->id],
            ['quantity' => 0, 'price' => $variant->price]
        );

        $item->quantity += $this->quantity;
        $item->save();

        $this->dispatch('cartUpdated');
        session()->flash('success', $variant->name . ' added to cart.');
    }
}
?>

{{-- ================= UI ================= --}}
<div class="space-y-2">

    <button
        wire:click="add"
        class="
            bg-[#8B5E3C]
            hover:bg-[#6F472E]
            text-white
            px-4 py-2
            rounded-md
            font-semibold
            transition
            shadow-sm
            hover:shadow-md
            focus:ring-2 focus:ring-[#38BDF8]
        "
    >
        Add to Cart
    </button>

    @if (session()->has('success'))
        <p class="text-[#2B2B2B] text-sm mt-2">
            {{ session('success') }}
        </p>
    @endif

</div>