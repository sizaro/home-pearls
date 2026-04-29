<?php

use Livewire\Component;

new class extends Component
{
    
};
?>


<div class="bg-[#8B5E3C] text-white h-full w-64 flex flex-col p-5">

    <h2 class="text-2xl font-bold mb-6">
        Admin
    </h2>

    <nav class="space-y-3">

        <a href="{{ route('admin.dashboard') }}" 
           class="block px-3 py-2 rounded hover:bg-gray-800 transition">
            Dashboard
        </a>

        <a href="{{ route('admin.categories') }}" 
           class="block px-3 py-2 rounded hover:bg-gray-800 transition">
            Categories
        </a>

        <a href="{{ route('admin.products') }}" 
           class="block px-3 py-2 rounded hover:bg-gray-800 transition">
            Products
        </a>

        <a href="{{ route('admin.product-variants') }}" 
           class="block px-3 py-2 rounded hover:bg-gray-800 transition">
            Variants
        </a>

    </nav>

</div>