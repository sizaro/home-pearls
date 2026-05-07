<?php

use Livewire\Component;
use App\Models\Product;

new class extends Component
{
    public array $products = [];
    public int $index = 0;

    public function mount()
    {
        $this->products = Product::orderBy('id', 'desc')
            ->take(6)
            ->with('variants')
            ->get()
            ->map(function ($product) {
                $variant = $product->variants->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->short_description,
                    'price' => $variant?->price,
                    'stock' => $product->stock ?? 0,
                    'image_url' => $product->image_url,
                ];
            })
            ->toArray();
    }

    public function next()
    {
        $this->index = ($this->index + 1) % count($this->products);
    }

    public function prev()
    {
        $this->index = ($this->index - 1 + count($this->products)) % count($this->products);
    }

    public function autoSlide()
    {
        $this->next();
    }
};

?>
<div
    class="relative w-full overflow-hidden bg-[#F6F1EB]"
    wire:poll.6000ms="autoSlide"
>

    @php
        $product = $this->products[$this->index] ?? null;
    @endphp

    @if($product)

    {{-- DESKTOP + TABLET --}}
    <div class="hidden md:grid grid-cols-2 h-[70vh]">

        {{-- LEFT SIDE (TEXT) --}}
        <div class="flex flex-col justify-center p-10 space-y-4">

            <h1 class="text-4xl font-bold text-[#3B2F2A]">
                {{ $product['name'] }}
            </h1>

            <p class="text-[#3B2F2A]/70">
                {{ $product['description'] }}
            </p>

            <p class="text-xl font-bold text-[#8B5E3C]">
                ${{ number_format($product['price'] ?? 0, 2) }}
            </p>

            <p class="text-sm text-gray-600">
                Stock: {{ $product['stock'] }}
            </p>

            <a href="{{ route('products.show', $product['id']) }}"
               class="w-fit bg-[#38BDF8] text-white px-6 py-3 rounded-lg hover:opacity-90 transition">
                Shop Now
            </a>

        </div>

        {{-- RIGHT SIDE (IMAGE FIXED) --}}
        <div class="w-full h-full flex items-center justify-center bg-white overflow-hidden">

            <img
                src="{{ $product['image_url'] ? route('products.image', $product['id']) : 'https://via.placeholder.com/800' }}"
                class="w-full h-full object-contain"
            >

        </div>

    </div>

    {{-- MOBILE --}}
    <div class="md:hidden relative h-[60vh] flex flex-col items-center p-4">

        <img
            src="{{ $product['image_url'] ? route('products.image', $product['id']) : 'https://via.placeholder.com/800' }}"
            class="w-full h-full object-contain"
        >

        {{-- OVERLAY --}}
        <div class="absolute bottom-0 w-full bg-black/10 text-white p-4 flex flex-col items-center">

            <h2 class="text-lg font-bold">
                {{ $product['name'] }}
            </h2>

            <p class="text-sm">
                ${{ number_format($product['price'] ?? 0, 2) }}
                • Stock: {{ $product['stock'] }}
            </p>

            <a href="{{ route('products.show', $product['id']) }}"
               class="inline-block mt-2 bg-[#38BDF8] px-4 py-2 rounded">
                Shop Now
            </a>

        </div>

    </div>

    {{-- CONTROLS --}}
    <button wire:click="prev"
        class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/70 px-3 py-2 rounded-full">
        ‹
    </button>

    <button wire:click="next"
        class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/70 px-3 py-2 rounded-full">
        ›
    </button>

    @else
        <div class="h-[60vh] flex items-center justify-center text-gray-500">
            No products available
        </div>
    @endif

</div>