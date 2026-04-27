<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    //
};
?>

<div x-data="{ open: false }" class="sticky top-0 z-50">

    <nav class="bg-white shadow-md border-b">

        <div class="max-w-7xl mx-auto px-4">

            {{-- MOBILE ROW --}}
            <div class="flex items-center justify-between h-16 lg:hidden">

                {{-- HAMBURGER (optional sidebar trigger) --}}
                <button @click="open = true" class="text-2xl">
                    ☰
                </button>

                {{-- BRAND --}}
                <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold">
                    Home Pearls Admin
                </a>

                {{-- USER ICON --}}
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

            {{-- DESKTOP ROW --}}
            <div class="hidden lg:flex items-center justify-between h-16">

                {{-- LOGO --}}
                <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold">
                    Home Pearls Admin
                </a>

                {{-- USER DROPDOWN --}}
                @auth
                <div x-data="{ openUser: false }" class="relative">

                    <button @click="openUser = !openUser" class="text-2xl">
                        👤
                    </button>

                    <div x-show="openUser"
                         @click.away="openUser = false"
                         class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-50">

                        <div class="px-4 py-2 text-sm text-gray-500">
                            {{ Auth::user()->name }}
                        </div>

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

    {{-- MOBILE SIDEBAR --}}
    <div
        x-show="open"
        x-transition
        @click.away="open = false"
        class="fixed inset-0 z-50 flex">

        <div class="w-72 bg-black shadow-lg">
            <livewire:admin.side-bar />
        </div>

        <div class="flex-1" @click="open = false"></div>
    </div>

</div>