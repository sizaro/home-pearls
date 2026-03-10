<?php

use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.products')] class extends Component
{
    // This receives the {product} from the route
    public string $productSlug = '';

    // Mock Products (no price here)
    protected array $products = [
        'luxury-wooden-bed' => [
            'name' => 'Luxury Wooden Bed',
            'description' => 'Premium hardwood bed frame with elegant finish.',
        ],

        'modern-metal-bed' => [
            'name' => 'Modern Metal Bed',
            'description' => 'Durable steel frame with minimalist design.',
        ],

        'office-chair' => [
            'name' => 'Office Chair',
            'description' => 'Ergonomic chair for long working hours.',
        ],
    ];

    // Variants (prices belong here)
    protected array $variants = [
        'luxury-wooden-bed' => [
            ['name' => 'Queen Size', 'price' => 1200],
            ['name' => 'King Size', 'price' => 1500],
        ],

        'modern-metal-bed' => [
            ['name' => 'Standard Size', 'price' => 900],
            ['name' => 'Premium Finish', 'price' => 1100],
        ],

        'office-chair' => [
            ['name' => 'Black', 'price' => 350],
            ['name' => 'Gray', 'price' => 380],
        ],
    ];

    public function mount($product)
    {
        $this->productSlug = $product;

        if (!array_key_exists($product, $this->products)) {
            abort(404);
        }
    }

    // Computed Product
    public function getProductProperty()
    {
        return $this->products[$this->productSlug];
    }

    // Computed Variants
    public function getProductVariantsProperty()
    {
        return $this->variants[$this->productSlug] ?? [];
    }
};
?>

<div class="max-w-5xl mx-auto">

    <h1 class="text-3xl font-bold mb-2">
        {{ $this->product['name'] }}
    </h1>

    <p class="text-gray-600 mb-8">
        {{ $this->product['description'] }}
    </p>

    <h2 class="text-xl font-semibold mb-4">
        Available Options
    </h2>

    @if(empty($this->productVariants))
        <p class="text-gray-500">No variants available.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($this->productVariants as $variant)
                <div class="bg-white p-6 rounded shadow">
                    <h3 class="font-semibold text-lg">
                        {{ $variant['name'] }}
                    </h3>

                    <p class="text-yellow-600 font-bold mt-2">
                        ${{ number_format($variant['price']) }}
                    </p>

                    <button class="mt-4 w-full bg-yellow-500 hover:bg-yellow-400 text-black py-2 rounded">
                        Add to Cart
                    </button>
                </div>
            @endforeach
        </div>
    @endif

</div>