<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    //
};
?>

<div
    x-data="{ open: false }"
    x-init="
        $watch('open', value => {
            document.body.style.overflow = value ? 'hidden' : 'auto'
        })
    "
    class="relative z-50"
>

    {{-- ================= NAVBAR ================= --}}
    <nav class="bg-white shadow-md border-b">

        <div class="max-w-7xl mx-auto px-4">

            <div class="flex items-center justify-between h-16">

                {{-- HAMBURGER --}}
                <button @click="open = true" class="text-2xl lg:hidden">
                    ☰
                </button>

                {{-- BRAND --}}
                <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold">
                    Home Pearls Admin
                </a>

                {{-- USER --}}
                @auth
                <div x-data="{ userOpen: false }" class="relative">

                    <button @click="userOpen = !userOpen" class="text-2xl">
                        👤
                    </button>

                    <div x-show="userOpen"
                         @click.away="userOpen = false"
                         class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-50">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>

                    </div>
                </div>
                @endauth

            </div>

        </div>
    </nav>

    {{-- ================= BACKDROP ================= --}}
    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-black/40 z-40"
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
        class="fixed top-0 left-0 h-full w-72 bg-[#8B5E3C] text-white shadow-2xl z-50 flex flex-col"
    >

        {{-- HEADER --}}
        <div class="flex justify-between items-center p-4 border-b border-white/20">
            <h2 class="font-bold text-lg">Admin Menu</h2>

            {{-- CLOSE BUTTON --}}
            <button @click="open = false" class="text-2xl lg:hidden">
                ✕
            </button>
        </div>

        {{-- CONTENT --}}
        <div class="p-4 space-y-3">

            <a href="{{ route('admin.dashboard') }}" class="block hover:text-blue-300">
                Dashboard
            </a>

            <a href="{{ route('admin.categories') }}" class="block hover:text-blue-300">
                Categories
            </a>

            <a href="{{ route('admin.products') }}" class="block hover:text-blue-300">
                Products
            </a>

            <a href="{{ route('admin.product-variants') }}" class="block hover:text-blue-300">
                Variants
            </a>

        </div>

    </aside>

</div>