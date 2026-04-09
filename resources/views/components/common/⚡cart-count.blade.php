
<?php
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\Cart;

new class extends Component
{
    public int $count = 0;

    protected $listeners = ['cartUpdated' => 'loadCount'];

    public function mount()
    {
        $this->loadCount();
    }

    public function loadCount()
    {
        $cart = null;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())
                ->where('status', 'active')
                ->with('items')
                ->first();
        } else {
            $guestCartId = Cookie::get('guest_cart_id');

            if ($guestCartId) {
                $cart = Cart::where('guest_cart_id', $guestCartId)
                    ->where('status', 'active')
                    ->with('items')
                    ->first();
            }
        }

        if ($cart) {
            $this->count = $cart->items->sum('quantity');
        } else {
            $this->count = 0;
        }
    }
}
?>

<div>
    <a href="{{ route('cart') }}" class="text-2xl relative">
    🛒
    
    @if($count > 0)
        <span class="absolute -top-2 -right-3 bg-yellow-500 text-xs px-1.5 rounded-full">
            {{ $count }}
        </span>
    @endif
</a>
</div>