<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

new #[Layout('layouts.admin')] class extends Component
{
    // Stats
    public int $categoriesCount = 0;
    public int $productsCount = 0;
    public int $variantsCount = 0;

    public function mount()
    {
        $this->categoriesCount = Category::count();
        $this->productsCount = Product::count();
        $this->variantsCount = ProductVariant::count();
    }
};
?>

<div class="space-y-8">

    {{-- PAGE TITLE --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-500">Manage your store</p>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-gray-500">Categories</h2>
            <p class="text-3xl font-bold">{{ $categoriesCount }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-gray-500">Products</h2>
            <p class="text-3xl font-bold">{{ $productsCount }}</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-gray-500">Variants</h2>
            <p class="text-3xl font-bold">{{ $variantsCount }}</p>
        </div>

    </div>

    {{-- QUICK ACTIONS --}}
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Quick Actions</h2>

        <div class="flex flex-wrap gap-4">
            <a href="{{ route('admin.categories') }}"
               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Manage Categories
            </a>

            <a href="{{ route('admin.products') }}"
               class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Manage Products
            </a>

            <a href="{{ route('admin.product-variants') }}"
               class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                Manage Variants
            </a>
        </div>
    </div>

</div>