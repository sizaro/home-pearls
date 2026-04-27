<?php

use Livewire\Component;

new class extends Component
{
    
};
?>

<div class="bg-gray-900 text-white h-screen hidden md:block">
    <aside class="w-64 bg-gray-900 text-white p-5 h-screen">
        <h2 class="text-2xl font-bold mb-6">Admin</h2>

        <nav class="space-y-3">
            <a href="{{ route('admin.dashboard') }}" class="block text-black hover:text-blue-400">Dashboard</a>
            <a href="{{ route('admin.categories') }}" class="block hover:text-blue-400">Categories</a>
            <a href="{{ route('admin.products') }}" class="block hover:text-blue-400">Products</a>
            <a href="{{ route('admin.product-variants') }}" class="block hover:text-blue-400">Variants</a>
        </nav>
    </aside>
</div>