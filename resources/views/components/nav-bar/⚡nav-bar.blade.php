<?php

use Livewire\Component;

new class extends Component
{
    public string $search = '';

    // 🔥 MOCKED DATA
    public array $categories = [
        [
            'name' => 'Beds',
            'slug' => 'beds',
            'products' => [
                [
                    'name' => 'Luxury Wooden Bed',
                    'variants' => [
                        ['name' => 'Queen Size - Brown', 'price' => 1200],
                        ['name' => 'King Size - Dark', 'price' => 1500],
                        ['name' => 'Single - Light', 'price' => 800],
                    ],
                ],
                [
                    'name' => 'Modern Metal Bed Frame',
                    'variants' => [
                        ['name' => 'Queen Metal - Black', 'price' => 900],
                        ['name' => 'King Metal - Silver', 'price' => 1100],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Chairs',
            'slug' => 'chairs',
            'products' => [
                [
                    'name' => 'Classic Wooden Chair',
                    'variants' => [
                        ['name' => 'Brown Wood', 'price' => 250],
                        ['name' => 'Dark Oak', 'price' => 300],
                    ],
                ],
                [
                    'name' => 'Steel Office Chair',
                    'variants' => [
                        ['name' => 'Black Steel', 'price' => 350],
                        ['name' => 'Ergonomic - Grey', 'price' => 450],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Metal Works',
            'slug' => 'metal',
            'products' => [
                [
                    'name' => 'Premium Metal Table',
                    'variants' => [
                        ['name' => 'Large - Black', 'price' => 700],
                        ['name' => 'Small - Silver', 'price' => 550],
                    ],
                ],
                [
                    'name' => 'Metal Shelf',
                    'variants' => [
                        ['name' => '4 Layer - Black', 'price' => 400],
                        ['name' => '2 Layer - Grey', 'price' => 280],
                    ],
                ],
            ],
        ],
    ];

    public function searchProducts()
    {
        if (trim($this->search) === '') return;

        return redirect()->route('products.index', [
            'search' => $this->search
        ]);
    }

    // 🔥 FILTER MOCKED DATA
    public function getFilteredResultsProperty()
    {
        if (!$this->search) {
            return [];
        }

        $term = strtolower($this->search);

        $results = [];

        foreach ($this->categories as $category) {

            // category match
            if (str_contains(strtolower($category['name']), $term)) {
                $results[] = [
                    'type' => 'Category',
                    'name' => $category['name'],
                ];
            }

            foreach ($category['products'] as $product) {

                // product match
                if (str_contains(strtolower($product['name']), $term)) {
                    $results[] = [
                        'type' => 'Product',
                        'name' => $product['name'],
                    ];
                }

                // variant match
                foreach ($product['variants'] as $variant) {
                    if (str_contains(strtolower($variant['name']), $term)) {
                        $results[] = [
                            'type' => 'Variant',
                            'name' => $variant['name'],
                            'price' => $variant['price'],
                        ];
                    }
                }
            }
        }

        return $results;
    }
};
?>

<div>
    
{{-- NAVBAR --}}
<nav class="bg-white shadow-md border-b">

    <div class="max-w-7xl mx-auto px-4">

        {{-- MOBILE TOP ROW --}}
        <div class="flex items-center justify-between h-16 lg:hidden">

            {{-- HAMBURGER --}}
            <button class="text-gray-700 text-2xl">
                ☰
            </button>

            {{-- LOGO --}}
            <a href="/" class="text-2xl font-bold text-gray-800">
                Home Pearls
            </a>

            {{-- ACCOUNT --}}
            <a href="#" class="text-gray-700 text-2xl">
                👤
            </a>

        </div>

        {{-- MOBILE SEARCH --}}
        <div class="lg:hidden pb-4">
            <form wire:submit.prevent="searchProducts">
                <div class="flex">

                    <input
                        type="text"
                        wire:model.defer="search"
                        placeholder="Search products..."
                        class="flex-1 border border-gray-300 rounded-l-full px-4 py-2"
                    >

                    <button
                        type="submit"
                        class="bg-yellow-500 text-black px-4 rounded-r-full"
                    >
                        🔍
                    </button>

                </div>
            </form>
        </div>

        {{-- DESKTOP ROW --}}
        <div class="hidden lg:flex items-center justify-between h-16">

            {{-- LOGO --}}
            <a href="/" class="text-2xl font-bold text-gray-800">
                Home Pearls
            </a>

            {{-- SEARCH --}}
            <div class="flex-1 mx-6">
                <form wire:submit.prevent="searchProducts">
                    <div class="flex">

                        <input
                            type="text"
                            wire:model.defer="search"
                            placeholder="Search beds, chairs, metal works..."
                            class="flex-1 border border-gray-300 rounded-l-full px-4 py-2 focus:outline-none"
                        >

                        <button
                            type="submit"
                            class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-full px-6 hover:bg-gray-200"
                        >
                            🔍
                        </button>

                    </div>
                </form>
            </div>

            {{-- CART --}}
            <a href="#" class="relative text-gray-700 hover:text-black text-2xl">
                🛒
                <span class="absolute -top-2 -right-3 bg-yellow-500 text-xs text-black px-1.5 rounded-full">
                    0
                </span>
            </a>

            {{-- ACCOUNT --}}
            <a href="#" class="text-gray-700 text-2xl ml-4">
                👤
            </a>

        </div>

    </div>
</nav>

{{-- SEARCH RESULTS (Optional preview) --}}
@if($this->filteredResults)
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 py-3">

            <h2 class="font-semibold text-gray-800 mb-2">
                Results for "{{ $search }}"
            </h2>

            <div class="space-y-2">
                @foreach($this->filteredResults as $result)
                    <div class="p-2 border rounded">
                        <span class="text-sm text-gray-500">{{ $result['type'] }}:</span>
                        <span class="font-medium">{{ $result['name'] }}</span>

                        @if(isset($result['price']))
                            <span class="text-yellow-600 font-bold">
                                - ${{ number_format($result['price']) }}
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endif
</div>
