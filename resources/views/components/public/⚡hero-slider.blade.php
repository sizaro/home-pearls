<?php

use Livewire\Component;

new class extends Component
{
    // 🔥 MOCKED SLIDES
    public array $slides = [
        [
            'title' => 'Luxury Wooden Beds',
            'subtitle' => 'Handcrafted quality and comfort',
            'image' => 'https://via.placeholder.com/1200x500?text=Wooden+Beds',
        ],
        [
            'title' => 'Modern Metal Beds',
            'subtitle' => 'Strong frames and elegant design',
            'image' => 'https://via.placeholder.com/1200x500?text=Metal+Beds',
        ],
        [
            'title' => 'Premium Chairs',
            'subtitle' => 'Comfort for home and office',
            'image' => 'https://via.placeholder.com/1200x500?text=Premium+Chairs',
        ],
        [
            'title' => 'Metal Furniture',
            'subtitle' => 'Built to last with modern style',
            'image' => 'https://via.placeholder.com/1200x500?text=Metal+Furniture',
        ],
    ];

    public int $index = 0;

    public function nextSlide()
    {
        $this->index = ($this->index + 1) % count($this->slides);
    }
};
?>

<div
    x-data
    x-init="setInterval(() => $wire.nextSlide(), 5000)"
    class="relative w-full overflow-hidden"
>

    @php
        $slide = $this->slides[$this->index];
    @endphp

    <div class="relative h-[450px] md:h-[550px]">

        {{-- BACKGROUND IMAGE --}}
        <img
            src="{{ $slide['image'] }}"
            class="w-full h-full object-cover"
        >

        {{-- OVERLAY --}}
        <div class="absolute inset-0 bg-black/40"></div>

        {{-- TEXT CONTENT --}}
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white">
            <h1 class="text-3xl md:text-5xl font-bold">
                {{ $slide['title'] }}
            </h1>

            <p class="mt-3 text-lg md:text-xl text-gray-200">
                {{ $slide['subtitle'] }}
            </p>

            <a href="#"
               class="mt-6 bg-yellow-500 text-black px-6 py-3 rounded font-semibold hover:bg-yellow-400">
                Shop Now
            </a>
        </div>

    </div>

    {{-- INDICATORS --}}
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
        @foreach($this->slides as $i => $s)
            <span
                wire:click="$set('index', {{ $i }})"
                class="w-3 h-3 rounded-full cursor-pointer
                    {{ $i === $this->index ? 'bg-yellow-500' : 'bg-white/50' }}"
            ></span>
        @endforeach
    </div>

</div>