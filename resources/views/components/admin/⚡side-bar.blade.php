<?php

use Livewire\Component;

new class extends Component
{
    
};
?>

<div class="bg-gray-900 text-white h-screen hidden md:block">
    <aside class="w-64 bg-gray-900 text-white p-5 h-screen">
        
        <h2 class="text-2xl font-bold mb-6 text-white">
            Admin
        </h2>

        <nav class="space-y-3">

            <a href="{{ route('admin.dashboard') }}" 
               class="block px-3 py-2 rounded hover:bg-gray-800 hover:text-blue-400 transition">
                Dashboard
            </a>

            <a href="{{ route('admin.categories') }}" 
               class="block px-3 py-2 rounded hover:bg-gray-800 hover:text-blue-400 transition">
                Categories
            </a>

            <a href="{{ route('admin.products') }}" 
               class="block px-3 py-2 rounded hover:bg-gray-800 hover:text-blue-400 transition">
                Products
            </a>

            <a href="{{ route('admin.product-variants') }}" 
               class="block px-3 py-2 rounded hover:bg-gray-800 hover:text-blue-400 transition">
                Variants
            </a>

        </nav>

    </aside>
</div>