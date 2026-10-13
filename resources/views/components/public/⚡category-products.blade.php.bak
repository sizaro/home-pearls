<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;

new #[Layout('layouts.products')] class extends Component
{
    public string $categorySlug = '';
    public ?Category $category = null;
    public array $products = [];
    public int $index = 0;

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

<div class="bg-[#F6F1EB] min-h-screen text-[#3B2F2A] space-y-10 px-4 py-6">

    {{-- CATEGORY TITLE --}}
    <h1 class="text-3xl font-bold capitalize text-[#3B2F2A]">
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
            class="relative w-full overflow-hidden rounded-xl shadow-lg bg-[#E7DED5]"
        >

            @if($currentProduct)
                <div class="relative h-[50vh] md:h-[60vh]">

                    <img
                        src="{{ $currentProduct['image_url']
                            ? route('products.image', ['id' => $currentProduct['id']])
                            : 'https://via.placeholder.com/1200x500' }}"
                        class="w-full h-full object-cover"
                    >

                    <div class="absolute inset-0 bg-black/30"></div>

                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white">

                        <h2 class="text-3xl md:text-5xl font-bold">
                            {{ $currentProduct['name'] }}
                        </h2>

                        <p class="mt-3 text-lg text-white/80 max-w-xl">
                            {{ $currentProduct['description'] }}
                        </p>

                        <a href="{{ route('products.show', $currentProduct['id']) }}"
                           class="mt-6 bg-[#38BDF8] text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                            View Product
                        </a>

                    </div>

                </div>

                {{-- DOTS --}}
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                    @foreach($products as $i => $p)
                        <span
                            wire:click="$set('index', {{ $i }})"
                            class="w-3 h-3 rounded-full cursor-pointer transition
                            {{ $i === $index ? 'bg-[#38BDF8]' : 'bg-white/50' }}"
                        ></span>
                    @endforeach
                </div>
            @endif

        </div>
    @endif

    {{-- PRODUCT GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach($products as $product)
            <a href="{{ route('products.show', $product['id']) }}"
               class="bg-[#E7DED5] rounded-xl overflow-hidden shadow hover:shadow-lg transition">

                <img
                    src="{{ $product['image_url']
                        ? route('products.image', ['id' => $product['id']])
                        : 'https://via.placeholder.com/400x300' }}"
                    class="w-full h-48 object-cover"
                >

                <div class="p-4">

                    <h3 class="font-semibold text-[#3B2F2A]">
                        {{ $product['name'] }}
                    </h3>

                    <p class="text-sm text-[#3B2F2A]/70 mt-2">
                        {{ $product['description'] }}
                    </p>

                    <span class="mt-3 inline-block text-[#38BDF8] font-medium">
                        View Product →
                    </span>

                </div>

            </a>
        @endforeach

    </div>

</div>