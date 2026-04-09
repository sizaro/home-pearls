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

    // Initialize with variant ID and optional quantity
    public function mount($variantId, $quantity = 1)
    {
        $this->variantId = $variantId;
        $this->quantity = $quantity;

        $this->initializeCart();
    }

    // Create or get cart for user or guest
    private function initializeCart()
    {
        if (Auth::check()) {
            // Logged-in user cart
            $this->cart = Cart::firstOrCreate([
                'user_id' => Auth::id(),
                'status' => 'active',
            ]);
        } else {
            // Guest user: check cookie
            $guestCartId = Cookie::get('guest_cart_id');

            if (!$guestCartId) {
                $guestCartId = (string) Str::uuid();
                // Store cookie for 30 days
                Cookie::queue('guest_cart_id', $guestCartId, 60 * 24 * 30);
            }

            // Load or create cart
            $this->cart = Cart::firstOrCreate([
                'guest_cart_id' => $guestCartId,
                'status' => 'active',
            ]);
        }
    }

    // Add variant to cart
    public function add()
    {
        $variant = ProductVariant::findOrFail($this->variantId);

        // Add or update cart item
        $item = $this->cart->items()->firstOrCreate(
            ['variant_id' => $variant->id],
            ['quantity' => 0, 'price' => $variant->price]
        );

        $item->quantity += $this->quantity;
        $item->save();

        $this->emit('cartUpdated');
        session()->flash('success', $variant->name . ' added to cart.');
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
?>

<div>
    <button 
        wire:click="add"
        class="bg-yellow-500 hover:bg-yellow-400 text-black px-4 py-2 rounded font-semibold"
    >
        Add to Cart
    </button>

    @if (session()->has('success'))
        <p class="text-green-600 mt-2">{{ session('success') }}</p>
    @endif
</div>