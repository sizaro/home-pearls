<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;

new #[Layout('layouts.products')] class extends Component
{
    // Live Product instance (from route model binding)
    public ?Product $product = null;

    public function mount(Product $product)
    {
        // Assign the model and eager-load variants
        $this->product = $product->load('variants');

        if (!$this->product) {
            abort(404);
        }
    }

    // Computed property for variants
    public function getProductVariantsProperty()
    {
        return $this->product->variants ?? collect();
    }
};
?>

<div class="max-w-5xl mx-auto">

    {{-- Product Image --}}
    <img
        src="{{ $this->product->image_url 
            ? route('products.image', ['id' => $this->product->id]) 
            : 'https://via.placeholder.com/800x400?text=No+Image' }}"
        class="w-full h-64 md:h-96 object-cover rounded mb-6"
    >

    <h1 class="text-3xl font-bold mb-2">{{ $this->product->name }}</h1>
    <p class="text-gray-600 mb-8">{{ $this->product->description }}</p>

    <h2 class="text-xl font-semibold mb-4">Available Options</h2>

    @if($this->productVariants->isEmpty())
        <p class="text-gray-500">No variants available.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($this->productVariants as $variant)
                <div class="bg-white p-6 rounded shadow">

                    {{-- Variant Image --}}
                    <img
                        src="{{ $variant->image_url 
                            ? route('product-variants.image', ['id' => $variant->id])
                            : 'https://via.placeholder.com/300x200?text=No+Image' }}"
                        class="w-full h-48 object-cover rounded mb-2"
                    >

                    <h3 class="font-semibold text-lg">{{ $variant->name }}</h3>
                    <p class="text-yellow-600 font-bold mt-2">${{ number_format($variant->price, 2) }}</p>

                    {{-- **Add to Cart Component** --}}
                    <livewire:public.add-to-cart :variant-id="$variant->id" :key="$variant->id" />
                    
                </div>
            @endforeach
        </div>
    @endif

</div>