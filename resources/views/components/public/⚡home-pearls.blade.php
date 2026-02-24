<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.public')] class extends Component
{
    // 🔥 MOCKED CATEGORIES
    public array $categories = [
        ['name' => 'Beds'],
        ['name' => 'Chairs'],
        ['name' => 'Metal Works'],
        ['name' => 'Tables'],
        ['name' => 'Sofas'],
    ];

    // 🔥 MOCKED FEATURED PRODUCTS
    public array $featured = [
        [
            'name' => 'Luxury Wooden Bed',
            'price' => 1200,
            'old_price' => 1500,
            'image' => 'https://via.placeholder.com/400x300?text=Wooden+Bed',
        ],
        [
            'name' => 'Modern Metal Bed',
            'price' => 900,
            'old_price' => 1100,
            'image' => 'https://via.placeholder.com/400x300?text=Metal+Bed',
        ],
        [
            'name' => 'Premium Office Chair',
            'price' => 350,
            'old_price' => 500,
            'image' => 'https://via.placeholder.com/400x300?text=Office+Chair',
        ],
        [
            'name' => 'Metal Coffee Table',
            'price' => 650,
            'old_price' => 800,
            'image' => 'https://via.placeholder.com/400x300?text=Coffee+Table',
        ],
    ];

    // 🔥 MOCKED TOP PRODUCTS
    public array $top = [
        [
            'name' => 'Ergonomic Chair',
            'price' => 450,
            'image' => 'https://via.placeholder.com/300x250?text=Ergonomic+Chair',
        ],
        [
            'name' => 'Luxury Bed Frame',
            'price' => 1300,
            'image' => 'https://via.placeholder.com/300x250?text=Bed+Frame',
        ],
        [
            'name' => 'Metal Storage Shelf',
            'price' => 280,
            'image' => 'https://via.placeholder.com/300x250?text=Storage+Shelf',
        ],
    ];

    // 🔥 MOCKED ADVERTS
    public array $adverts = [
        'Free delivery on orders above $500',
        'Custom furniture designs available',
        'Limited time discounts on beds',
    ];
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

                    <img src="{{ $product['image'] }}"
                         class="w-full h-48 object-cover rounded">

                    <h3 class="mt-3 font-semibold text-gray-800">
                        {{ $product['name'] }}
                    </h3>

                    <p class="mt-1">
                        <span class="text-yellow-600 font-bold">
                            ${{ number_format($product['price']) }}
                        </span>
                        <span class="text-gray-400 line-through ml-2">
                            ${{ number_format($product['old_price']) }}
                        </span>
                    </p>

                    <button class="mt-3 w-full bg-yellow-500 hover:bg-yellow-400 text-black py-2 rounded">
                        Add to Cart
                    </button>

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

                    <img src="{{ $product['image'] }}"
                         class="w-full h-40 object-cover rounded">

                    <h3 class="mt-3 font-semibold text-gray-800">
                        {{ $product['name'] }}
                    </h3>

                    <p class="text-yellow-600 font-bold">
                        ${{ number_format($product['price']) }}
                    </p>

                    <button class="mt-3 w-full bg-yellow-500 hover:bg-yellow-400 text-black py-2 rounded">
                        View
                    </button>

                </div>
            @endforeach
        </div>
    </div>

</div>