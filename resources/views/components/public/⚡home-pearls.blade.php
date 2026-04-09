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

<div class="space-y-10">

    {{-- ADVERT BANNER --}}
    <div class="bg-yellow-100 border border-yellow-300 p-4 rounded text-center text-yellow-800">
        @foreach($this->adverts as $ad)
            <p>{{ $ad }}</p>
        @endforeach
    </div>

    {{-- CATEGORIES --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Categories</h2>

        <div class="flex flex-wrap gap-3">
            @foreach($this->categories as $category)
                <a href="/products?category={{ strtolower($category['name']) }}"
                   class="px-4 py-2 bg-white border rounded hover:bg-gray-50">
                    {{ $category['name'] }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- FEATURED PRODUCTS --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Featured Products</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($this->featured as $product)
                <div class="bg-white rounded shadow hover:shadow-lg transition p-4">

                    <img
                src="{{ $product['image_url'] 
                    ? route('products.image', ['id' => $product['id']]) 
                    : 'https://via.placeholder.com/1200x500' }}"
                class="w-full h-40 object-cover"
            >

                    <h3 class="mt-3 font-semibold text-gray-800">
                        {{ $product['name'] }}
                    </h3>

                    @if(!is_null($product['price']))
                    <p class="mt-1 text-yellow-600 font-bold">
                        ${{ number_format($product['price'], 2) }}
                    </p>
                    @endif

                    <a href="{{ route('products.show', $product['id']) }}"
                       class="mt-3 w-full inline-block bg-yellow-500 hover:bg-yellow-400 text-black py-2 rounded text-center">
                        View
                    </a>

                </div>
            @endforeach
        </div>
    </div>

    {{-- TOP PRODUCTS --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Top Products</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->top as $product)
                <div class="bg-white rounded shadow p-4">

                     <img
                src="{{ $product['image_url'] 
                    ? route('products.image', ['id' => $product['id']]) 
                    : 'https://via.placeholder.com/1200x500' }}"
                class="w-full h-40 object-cover"
            >

                    <h3 class="mt-3 font-semibold text-gray-800">
                        {{ $product['name'] }}
                    </h3>

                    @if(!is_null($product['price']))
                    <p class="text-yellow-600 font-bold">
                        ${{ number_format($product['price'], 2) }}
                    </p>
                    @endif

                    <a href="{{ route('products.show', $product['id']) }}"
                       class="mt-3 w-full inline-block bg-yellow-500 hover:bg-yellow-400 text-black py-2 rounded text-center">
                        View
                    </a>

                </div>
            @endforeach
        </div>
    </div>

</div>