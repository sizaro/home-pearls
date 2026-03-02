<?php

use Livewire\Component;

new class extends Component
{
    public string $search = '';

    public function searchProducts()
    {
        if (trim($this->search) === '') {
            return;
        }

        return redirect()->route('products', [
            'search' => $this->search
        ]);
    }
};
?>

<div x-data="{ open: false }" class="sticky top-0 z-50">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow-md border-b">

        <div class="max-w-7xl mx-auto px-4">

            {{-- MOBILE ROW --}}
            <div class="flex items-center justify-between h-16 lg:hidden">

                {{-- HAMBURGER --}}
                <button @click="open = true" class="text-2xl">
                    ☰
                </button>

                <a href="{{ route('home-pearls') }}" class="text-2xl font-bold">
                    Home Pearls
                </a>

                <a href="#" class="text-2xl">
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
                            class="flex-1 border px-4 py-2"
                        >
                        <button type="submit" class="bg-yellow-500 px-4">
                            🔍
                        </button>
                    </div>
                </form>
            </div>

            {{-- DESKTOP ROW --}}
            <div class="hidden lg:flex items-center justify-between h-16">

                {{-- LOGO --}}
                <a href="{{ route('home-pearls') }}" class="text-2xl font-bold">
                    Home Pearls
                </a>

                {{-- SEARCH --}}
                <div class="flex-1 mx-6">
                    <form wire:submit.prevent="searchProducts">
                        <div class="flex">
                            <input
                                type="text"
                                wire:model.defer="search"
                                placeholder="Search beds, chairs..."
                                class="flex-1 border px-4 py-2"
                            >
                            <button type="submit" class="bg-gray-100 px-6">
                                🔍
                            </button>
                        </div>
                    </form>
                </div>

                {{-- CART (logic later) --}}
                <a href="#" class="text-2xl relative">
                    🛒
                    <span class="absolute -top-2 -right-3 bg-yellow-500 text-xs px-1.5 rounded-full">
                        0
                    </span>
                </a>

                {{-- ACCOUNT --}}
                <a href="#" class="text-2xl ml-4">
                    👤
                </a>

            </div>

        </div>
    </nav>

    {{-- MOBILE SIDEBAR --}}
    <div
        x-show="open"
        x-transition
        @click.away="open = false"
        class="fixed inset-0 z-50 flex">

        <div class="w-72 bg-white shadow-lg">
            @include('components.public.side-bar')
        </div>

        <div class="flex-1" @click="open = false"></div>
    </div>

</div>