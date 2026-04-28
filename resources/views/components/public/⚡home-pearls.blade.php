<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;
use App\Models\Product;

new #[Layout('layouts.public')] class extends Component
{
    public array $categories = [];
    public array $featured = [];
    public array $top = [];
    public array $adverts = [
        'Free delivery on orders above $500',
        'Custom furniture designs available',
        'Limited time discounts on beds',
    ];

    public function mount()
    {
        // Fetch categories
        $this->categories = Category::orderBy('name')
                                    ->get(['name'])
                                    ->toArray();

        // Featured products (latest 8 products)
        $this->featured = Product::orderBy('id', 'desc')
                                 ->take(8)
                                 ->with('variants')
                                 ->get()
                                 ->map(function($product) {
                                     $variant = $product->variants->first();
                                     return [
                                         'id' => $product->id,
                                         'name' => $product->name,
                                         'image_url' => $product->image_url,
                                         'price' => $variant ? $variant->price : null,
                                     ];
                                 })->toArray();

        // Top products (latest 6 products)
        $this->top = Product::orderBy('id', 'desc')
                            ->take(6)
                            ->with('variants')
                            ->get()
                            ->map(function($product) {
                                $variant = $product->variants->first();
                                return [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'image_url' => $product->image_url,
                                    'price' => $variant ? $variant->price : null,
                                ];
                            })->toArray();
    }
};
?>


<div class="space-y-12 bg-[#F5F1ED] min-h-screen px-6 py-10">

    {{-- ADVERT BANNER --}}
    <div class="bg-[#D4A373]/20 border border-[#D4A373]/40 p-4 rounded text-center text-[#5C3A21]">
        @foreach($this->adverts as $ad)
            <p>{{ $ad }}</p>
        @endforeach
    </div>

    {{-- CATEGORIES --}}
<div>
    <h2 class="text-2xl font-bold text-[#5C3A21] mb-4">
        Categories
    </h2>

    <div class="flex gap-3 overflow-x-auto whitespace-nowrap pb-2">

        @foreach($this->categories as $category)
            <a href="/products?category={{ strtolower($category['name']) }}"
               class="inline-block px-4 py-2 bg-white border border-[#EDE6DF] rounded hover:bg-[#F5F1ED] text-[#5C3A21] transition">
                {{ $category['name'] }}
            </a>
        @endforeach

    </div>
</div>

    {{-- FEATURED PRODUCTS --}}
    <div>
        <h2 class="text-2xl font-bold text-[#5C3A21] mb-4">Featured Products</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($this->featured as $product)
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4">

                    <img
                        src="{{ $product['image_url'] 
                            ? route('products.image', ['id' => $product['id']]) 
                            : 'https://via.placeholder.com/1200x500' }}"
                        class="w-full h-40 object-cover rounded"
                    >

                    <h3 class="mt-3 font-semibold text-[#5C3A21]">
                        {{ $product['name'] }}
                    </h3>

                    @if(!is_null($product['price']))
                    <p class="mt-1 text-[#D4A373] font-bold">
                        ${{ number_format($product['price'], 2) }}
                    </p>
                    @endif

                    <a href="{{ route('products.show', $product['id']) }}"
                       class="mt-3 w-full inline-block bg-[#8B5E3C] hover:bg-[#5C3A21] text-white py-2 rounded text-center transition">
                        View
                    </a>

                </div>
            @endforeach
        </div>
    </div>

    {{-- TOP PRODUCTS --}}
    <div>
        <h2 class="text-2xl font-bold text-[#5C3A21] mb-4">Top Products</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->top as $product)
                <div class="bg-white rounded-xl shadow p-4 hover:shadow-lg transition">

                    <img
                        src="{{ $product['image_url'] 
                            ? route('products.image', ['id' => $product['id']]) 
                            : 'https://via.placeholder.com/1200x500' }}"
                        class="w-full h-40 object-cover rounded"
                    >

                    <h3 class="mt-3 font-semibold text-[#5C3A21]">
                        {{ $product['name'] }}
                    </h3>

                    @if(!is_null($product['price']))
                    <p class="text-[#D4A373] font-bold">
                        ${{ number_format($product['price'], 2) }}
                    </p>
                    @endif

                    <a href="{{ route('products.show', $product['id']) }}"
                       class="mt-3 w-full inline-block bg-[#8B5E3C] hover:bg-[#5C3A21] text-white py-2 rounded text-center transition">
                        View
                    </a>

                </div>
            @endforeach
        </div>
    </div>

</div>