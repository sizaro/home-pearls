<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.products')] class extends Component
{
    public string $search = '';

    // Mock categories
    public array $categories = [
        ['name' => 'Beds', 'slug' => 'beds'],
        ['name' => 'Chairs', 'slug' => 'chairs'],
        ['name' => 'Metal Works', 'slug' => 'metal-works'],
        ['name' => 'Tables', 'slug' => 'tables'],
        ['name' => 'Sofas', 'slug' => 'sofas'],
    ];

    // Mock products by category
    public array $products = [
        'beds' => [
            ['name' => 'Luxury Wooden Bed', 'price' => 1200],
            ['name' => 'Modern Metal Bed', 'price' => 900],
        ],
        'chairs' => [
            ['name' => 'Office Chair', 'price' => 350],
            ['name' => 'Ergonomic Chair', 'price' => 450],
        ],
        'metal-works' => [
            ['name' => 'Metal Shelf', 'price' => 280],
            ['name' => 'Metal Gate', 'price' => 1500],
        ],
        'tables' => [
            ['name' => 'Dining Table', 'price' => 800],
            ['name' => 'Coffee Table', 'price' => 450],
        ],
        'sofas' => [
            ['name' => 'Leather Sofa', 'price' => 2000],
            ['name' => 'Fabric Sofa', 'price' => 1200],
        ],
    ];

    public function mount()
    {
        // Search string from URL
        $this->search = request()->query('search', '');
    }

    // Filter products by search term (categories + product names)
    public function getFilteredProductsProperty()
    {
        $term = strtolower($this->search);

        if (!$term) {
            return [];
        }

        $results = [];

        foreach ($this->products as $category => $items) {
            foreach ($items as $product) {
                if (
                    str_contains(strtolower($product['name']), $term)
                ) {
                    $results[] = $product;
                }
            }
        }

        return $results;
    }
};
?>

<div>

    <h1 class="text-2xl font-bold mb-6">
        Results for "{{ $search }}"
    </h1>

    @if(empty($this->filteredProducts))
        <p class="text-gray-500">No products found.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->filteredProducts as $product)
                <div class="bg-white rounded shadow p-4">
                    <h3 class="font-semibold text-gray-800">
                        {{ $product['name'] }}
                    </h3>

                    <p class="text-yellow-600 font-bold mt-2">
                        ${{ number_format($product['price']) }}
                    </p>

                    <button class="mt-3 w-full bg-yellow-500 hover:bg-yellow-400 text-black py-2 rounded">
                        Add to Cart
                    </button>
                </div>
            @endforeach
        </div>
    @endif

</div>