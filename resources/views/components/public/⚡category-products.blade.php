<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;
use App\Models\Product;

new #[Layout('layouts.products')] class extends Component
{
    public string $categorySlug = '';
    public ?Category $category = null;
    public array $products = [];
    public int $index = 0; // slider index

    public function mount(Category $category)
{
    $this->category = $category;

    $this->products = $category->products()
        ->latest()
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->short_description,
                'image_url' => $product->image_url,
            ];
        })
        ->toArray();
}
    public function nextSlide()
    {
        if (count($this->products) === 0) return;
        $this->index = ($this->index + 1) % count($this->products);
    }
};
?>

<div class="space-y-6">

    {{-- CATEGORY TITLE --}}
    <h1 class="text-2xl font-bold mb-6 capitalize">
        {{ $category->name }}
    </h1>

    {{-- SLIDER --}}
    @if(!empty($products))
        @php
            $currentProduct = $products[$index] ?? null;
        @endphp

        <div
            x-data
            x-init="setInterval(() => $wire.nextSlide(), 5000)"
            class="relative w-full overflow-hidden mb-6"
        >
            @if($currentProduct)
                <div class="relative h-[50vh] md:h-[60vh]">
                    <img
                        src="{{ $currentProduct['image_url'] 
                            ? route('products.image', ['id' => $currentProduct['id']]) 
                            : 'https://via.placeholder.com/1200x500?text=No+Image' }}"
                        class="w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-black/40"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white">
                        <h2 class="text-3xl md:text-5xl font-bold">
                            {{ $currentProduct['name'] }}
                        </h2>
                        <p class="mt-3 text-lg md:text-xl text-gray-200">
                            {{ $currentProduct['description'] }}
                        </p>
                        <a href="{{ route('products.show', $currentProduct['id']) }}"
                           class="mt-6 bg-yellow-500 text-black px-6 py-3 rounded font-semibold hover:bg-yellow-400">
                            View Product
                        </a>
                    </div>
                </div>

                {{-- SLIDER INDICATORS --}}
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                    @foreach($products as $i => $p)
                        <span
                            wire:click="$set('index', {{ $i }})"
                            class="w-3 h-3 rounded-full cursor-pointer
                                {{ $i === $index ? 'bg-yellow-500' : 'bg-white/50' }}"
                        ></span>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- PRODUCT GRID --}}
    @if(!empty($products))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                <a href="{{ route('products.show', $product['id']) }}"
                   class="block bg-white rounded shadow hover:shadow-lg transition overflow-hidden">
                    <img
                        src="{{ $product['image_url'] 
                            ? route('products.image', ['id' => $product['id']]) 
                            : 'https://via.placeholder.com/400x300?text=No+Image' }}"
                        class="w-full h-48 object-cover"
                    >
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800">{{ $product['name'] }}</h3>
                        <p class="text-gray-600 text-sm mt-2">{{ $product['description'] }}</p>
                        <span class="mt-3 inline-block text-yellow-600 font-medium">View Product</span>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No products found in this category.</p>
    @endif

</div>