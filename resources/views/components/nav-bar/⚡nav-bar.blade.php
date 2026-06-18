<?php

use Livewire\Component;

new class extends Component
{
    public string $search = '';

    public function searchProducts()
    {
        if (trim($this->search) === '') return;

        return redirect()->route('products', [
            'search' => $this->search
        ]);
    }
};
?>

<div
    x-data="{ open: false }"
    x-init="
        $watch('open', value => {
            document.body.style.overflow = value ? 'hidden' : 'auto'
        })
    "
    class="relative z-50 w-screen overflow-x-hidden"
>

    {{-- ================= NAVBAR ================= --}}
    <nav class="bg-[#F5F1ED] border-b border-[#8B5E3C]/20 relative z-50 overflow-x-hidden">
        <div class="max-w-7xl mx-auto px-4">

            <div class="flex items-center justify-between h-16">

                {{-- HAMBURGER --}}
                <button
                    @click="open = true"
                    class="text-3xl text-[#8B5E3C] lg:hidden"
                >
                    ☰
                </button>

                {{-- BRAND --}}
                {{-- BRAND --}}
<a href="{{ route('home-pearls') }}"
   class="flex items-center gap-2 text-xl font-bold text-[#8B5E3C]">

    <img src="{{ asset('images/homepearls_logo.webp') }}"
         alt="Home Pearls Logo"
         class="w-8 h-8 object-contain">

    <span>Home Pearls</span>
</a>

                {{-- DESKTOP SEARCH --}}
                <form
                    wire:submit.prevent="searchProducts"
                    class="hidden lg:flex flex-1 mx-6"
                >
                    <input
                        type="text"
                        wire:model.defer="search"
                        placeholder="Search furniture..."
                        class="w-full px-4 py-2 rounded-l border border-[#8B5E3C]/20 bg-white text-[#8B5E3C] focus:outline-none"
                    >
                    <button class="bg-[#38BDF8] text-white px-5 rounded-r">
                        🔍
                    </button>
                </form>

                 {{-- CART --}}
            <livewire:common.cart-count />

                {{-- LOGIN ICON --}}
                <div class="flex items-center gap-4 p-2">

    @auth

        @php
            $user = Auth::user();

            $dashboard = match(true) {
                $user->hasRole('super admin') => route('admin.dashboard'),
                $user->hasRole('admin') => route('admin.dashboard'),
                default => route('dashboard'),
            };
        @endphp

        <a href="{{ $dashboard }}"
           class="text-[#8B5E3C] text-2xl hover:text-[#38BDF8] transition">
            👤
        </a>

    @else

        <a href="{{ route('login') }}"
           class="text-[#8B5E3C] text-2xl hover:text-[#38BDF8] transition">
            👤
        </a>

    @endauth

</div>

            </div>
            <div class="md:hidden flex">
                     <form
                    wire:submit.prevent="searchProducts"
                    class="flex flex-1 mx-6"
                >
                    <input
                        type="text"
                        wire:model.defer="search"
                        placeholder="Search furniture..."
                        class="w-full px-4 py-2 rounded-l border border-[#8B5E3C]/20 bg-white text-[#8B5E3C] focus:outline-none"
                    >
                    <button class="bg-[#38BDF8] text-white px-5 rounded-r">
                        🔍
                    </button>
                </form>
                </div>

        </div>
    </nav>

    {{-- ================= BACKDROP (IMPORTANT FIX) ================= --}}
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40"
        @click="open = false"
    ></div>

    {{-- ================= SIDEBAR ================= --}}
    <aside
        x-show="open"
        x-transition:enter="transition transform duration-300 ease-out"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition transform duration-200 ease-in"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"

        class="fixed top-0 left-0 h-full w-[80%] max-w-sm bg-[#8B5E3C] text-white shadow-2xl flex flex-col z-50"
        @click.away="open = false"
    >

        {{-- HEADER --}}
        <div class="flex justify-between items-center p-4 border-b border-white/20">
            <h2 class="font-bold text-lg">Categories</h2>

            <button
                @click="open = false"
                class="text-2xl"
            >
                ✕
            </button>
        </div>

        {{-- SEARCH --}}
        <div class="p-4 border-b border-white/10">
            <form wire:submit.prevent="searchProducts" class="flex">
                <input
                    type="text"
                    wire:model.defer="search"
                    placeholder="Search..."
                    class="w-full px-3 py-2 rounded-l text-[#8B5E3C] bg-[#F5F1ED] outline-none"
                >
                <button class="bg-[#38BDF8] text-white px-4 rounded-r">
                    🔍
                </button>
            </form>
        </div>

        {{-- CONTENT --}}
        <div class="p-4 overflow-y-auto flex-1">
            <livewire:public.side-bar />
        </div>

    </aside>

</div>