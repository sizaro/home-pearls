<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Visit;

new #[Layout('layouts.public')] class extends Component
{
    public array $categories = [];
    public array $products = [];
    public array $productVariants = [];

    public array $adverts = [
        'Free delivery on orders above $500',
        'Custom furniture designs available',
        'Limited time discounts on beds',
    ];

    public function mount()
    {
        $ip = request()->ip();
        $today = now()->toDateString();

        $exists = Visit::where('ip', $ip)
            ->whereDate('created_at', $today)
            ->exists();

        if (!$exists) {
            Visit::create([
                'ip' => $ip,
                'url' => request()->fullUrl(),
            ]);
        }

        $this->categories = Category::orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        $this->products = Product::orderBy('id', 'desc')
            ->take(10)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'image_url' => $p->image_url,
            ])->toArray();

        $this->productVariants = ProductVariant::with('product')
            ->orderBy('id', 'desc')
            ->take(12)
            ->get()
            ->map(fn ($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'price' => $v->price,
                'stock' => $v->stock,
                'image_url' => $v->image_url,
                'product_name' => $v->product->name ?? '',
            ])->toArray();
    }
};
?>

{{-- =========================
PAGE WRAPPER (SAFE INSIDE LAYOUT)
========================= --}}
<div class="w-full max-w-7xl mx-auto space-y-12 px-4 md:px-6 py-6">

    {{-- =========================
        ADVERT BANNER
    ========================== --}}
    <div class="bg-[#D4A373]/20 border border-[#D4A373]/40 p-3 text-center text-[#5C3A21] rounded">
        @foreach($this->adverts as $ad)
            <p>{{ $ad }}</p>
        @endforeach
    </div>

    {{-- =========================
        CATEGORIES
    ========================== --}}
    <div>
        <h2 class="text-xl font-bold text-[#5C3A21] mb-3">Categories</h2>

        <div class="flex gap-3 overflow-x-auto pb-2 whitespace-nowrap">
            @foreach($this->categories as $cat)
                <a href="/products?category={{ strtolower($cat['name']) }}"
                   class="px-4 py-2 bg-white border rounded-full text-[#5C3A21] hover:bg-[#F5F1ED] transition">
                    {{ $cat['name'] }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- =========================
        PRODUCTS (HORIZONTAL SCROLL)
    ========================== --}}
    <div>
        <h2 class="text-xl font-bold text-[#5C3A21] mb-3">Products</h2>

        <div class="flex gap-4 overflow-x-auto pb-2">

            @foreach($this->products as $product)
                <div class="min-w-[220px] bg-white rounded-xl shadow p-3">

                    <img
                        src="{{ route('products.image', ['id' => $product['id']]) }}"
                        class="w-full h-40 object-cover rounded"
                    >

                    <h3 class="mt-2 font-semibold text-[#3B2F2A]">
                        {{ $product['name'] }}
                    </h3>

                    <a href="{{ route('products.show', $product['id']) }}"
                       class="mt-3 inline-block w-full text-center bg-[#8B5E3C] hover:bg-[#6F472E]
                              text-white px-4 py-2 rounded-md font-semibold transition">
                        View
                    </a>

                </div>
            @endforeach

        </div>
    </div>

    {{-- =========================
        VARIANTS (GRID)
    ========================== --}}
    <div>
        <h2 class="text-xl font-bold text-[#5C3A21] mb-3">Shop Items</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            @foreach($this->productVariants as $variant)
                <div class="bg-white rounded-xl shadow p-4">

                    <img
                        src="{{ $variant['image_url']
                            ? route('product-variants.image', $variant['id'])
                            : 'https://via.placeholder.com/300' }}"
                        class="w-full h-48 object-cover rounded"
                    >

                    <h3 class="mt-2 font-semibold text-[#3B2F2A]">
                        {{ $variant['name'] }}
                    </h3>

                    <p class="text-xs text-gray-500">
                        {{ $variant['product_name'] }}
                    </p>

                    <p class="text-[#38BDF8] font-bold mt-1">
                        ${{ number_format($variant['price'], 2) }}
                    </p>

                    <p class="text-xs text-gray-500">
                        Stock: {{ $variant['stock'] }}
                    </p>

                    <div class="mt-3">
                        <livewire:public.add-to-cart
                            :variant-id="$variant['id']"
                            :key="$variant['id']"
                        />
                    </div>

                </div>
            @endforeach

        </div>
    </div>

</div>