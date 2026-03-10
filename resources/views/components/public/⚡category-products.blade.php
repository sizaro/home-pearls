<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.products')] class extends Component
{
    public string $category = '';

    // Mock products grouped by category
    public array $products = [

        'beds' => [
            [
                'slug' => 'luxury-wooden-bed',
                'name' => 'Luxury Wooden Bed',
                'description' => 'Premium wooden bed frame',
            ],
            [
                'slug' => 'modern-metal-bed',
                'name' => 'Modern Metal Bed',
                'description' => 'Strong steel structure bed',
            ],
        ],

        'chairs' => [
            [
                'slug' => 'office-chair',
                'name' => 'Office Chair',
                'description' => 'Ergonomic office chair',
            ],
            [
                'slug' => 'dining-chair',
                'name' => 'Dining Chair',
                'description' => 'Wood & metal dining chair',
            ],
        ],

        'sofas' => [
            [
                'slug' => 'leather-sofa',
                'name' => 'Leather Sofa',
                'description' => 'Luxury leather sofa',
            ],
            [
                'slug' => 'fabric-sofa',
                'name' => 'Fabric Sofa',
                'description' => 'Comfortable fabric sofa',
            ],
        ],

    ];

    public function mount($category)
    {
        $this->category = $category;

        // Optional safety fallback
        if (!array_key_exists($category, $this->products)) {
            abort(404);
        }
    }

    public function getFilteredProductsProperty()
    {
        return $this->products[$this->category] ?? [];
    }
};
?>

<div>
    <h1 class="text-2xl font-bold mb-6 capitalize">
        {{ str_replace('-', ' ', $category) }}
    </h1>

    @if(empty($this->filteredProducts))
        <p class="text-gray-500">No products found in this category.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($this->filteredProducts as $product)
                <a href="{{ route('products.show', $product['slug']) }}"
                   class="block bg-white rounded shadow p-4 hover:shadow-lg transition">

                    <h3 class="font-semibold text-gray-800">
                        {{ $product['name'] }}
                    </h3>

                    <p class="text-gray-600 text-sm mt-2">
                        {{ $product['description'] }}
                    </p>

                    <span class="mt-3 inline-block text-yellow-600 font-medium">
                        View Options
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</div>