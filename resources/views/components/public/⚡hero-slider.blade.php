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
            ->take(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'short_description' => $product->short_description,
                    'image_url' => $product->image_url,
                ];
            })
            ->toArray();

        // Safety check
        if (count($this->products) === 0) {
            $this->index = 0;
        }
    }

    public function nextSlide()
    {
        if (count($this->products) === 0) return;

        $this->index = ($this->index + 1) % count($this->products);
    }
};
?>

<div class="relative w-full overflow-hidden bg-[#F6F1EB]">

    @php
        $product = $this->products[$this->index] ?? null;
    @endphp

    @if($product)
        <div class="relative h-[50vh] md:h-[60vh] w-full">

            {{-- IMAGE --}}
            <img
                src="{{ $product['image_url'] 
                    ? route('products.image', ['id' => $product['id']]) 
                    : 'https://via.placeholder.com/1200x500' }}"
                class="w-full h-full object-cover"
            >

            {{-- OVERLAY --}}
            <div class="absolute inset-0 bg-black/30"></div>

            {{-- TEXT --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white px-4">

                <h1 class="text-3xl md:text-5xl font-bold">
                    {{ $product['name'] }}
                </h1>

                <p class="mt-3 text-lg md:text-xl text-white/80 max-w-2xl">
                    {{ $product['short_description'] ?? '' }}
                </p>

                <a href="{{ route('products.show', $product['id']) }}"
                   class="mt-6 bg-[#38BDF8] text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                    Shop Now
                </a>

            </div>

        </div>
    @else
        <div class="h-[50vh] flex items-center justify-center text-[#3B2F2A]/60">
            No products available
        </div>
    @endif

    {{-- INDICATORS --}}
    @if(count($this->products) > 0)
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">

            @foreach($this->products as $i => $p)
                <span
                    wire:click="$set('index', {{ $i }})"
                    class="w-3 h-3 rounded-full cursor-pointer transition
                    {{ $i === $this->index ? 'bg-[#38BDF8]' : 'bg-white/50' }}"
                ></span>
            @endforeach

        </div>
    @endif

</div>