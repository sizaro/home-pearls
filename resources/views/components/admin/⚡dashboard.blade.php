<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;

new #[Layout('layouts.admin')] class extends Component
{
    public int $categoriesCount = 0;
    public int $productsCount = 0;
    public int $variantsCount = 0;
    public int $newOrdersCount = 0;

    public array $productsPerCategory = [];
    public array $variantsPerProduct = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->categoriesCount = Category::count();
        $this->productsCount   = Product::count();
        $this->variantsCount   = ProductVariant::count();

        $this->newOrdersCount = Order::where('status', 'pending')->count();

        // ✅ SAFE DATA (prevents empty/null issues)
        $this->productsPerCategory = Category::withCount('products')
            ->get()
            ->mapWithKeys(function ($cat) {
                return [$cat->name ?? 'Unknown' => $cat->products_count];
            })
            ->toArray();

        $this->variantsPerProduct = Product::withCount('variants')
            ->get()
            ->mapWithKeys(function ($prod) {
                return [$prod->name ?? 'Unknown' => $prod->variants_count];
            })
            ->toArray();

        // ✅ SEND TO JS
        $this->dispatch('chartsUpdated', [
            'productsPerCategory' => $this->productsPerCategory,
            'variantsPerProduct' => $this->variantsPerProduct,
        ]);
    }
};
?>

<div class="md:space-y-8 p-6 bg-[#F6F1EB] min-h-screen text-[#3B2F2A] space-y-8">

    {{-- TITLE --}}
    <div>
        <h1 class="text-3xl font-bold">Admin Dashboard</h1>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <div class="bg-white p-4 rounded shadow">
            <h2>Orders</h2>
            <p class="text-2xl text-[#38BDF8]">{{ $newOrdersCount }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2>Categories</h2>
            <p class="text-2xl">{{ $categoriesCount }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2>Products</h2>
            <p class="text-2xl">{{ $productsCount }}</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2>Variants</h2>
            <p class="text-2xl">{{ $variantsCount }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow border border-[#3B2F2A]/10">
    <h2 class="text-xl font-bold text-[#3B2F2A] mb-4">Quick Actions</h2>

    <div class="flex flex-wrap gap-4">

        <a href="{{ route('admin.categories') }}"
           class="bg-[#3B2F2A] text-white px-4 py-2 rounded-lg">
            Manage Categories
        </a>

        <a href="{{ route('admin.products') }}"
           class="bg-[#38BDF8] text-white px-4 py-2 rounded-lg">
            Manage Products
        </a>

        <a href="{{ route('admin.product-variants') }}"
           class="bg-[#8B5E3C] text-white px-4 py-2 rounded-lg">
            Manage Variants
        </a>

        @role('super admin')
        <a href="{{ route('admin.users') }}"
           class="bg-[#E7DED5] text-[#3B2F2A] px-4 py-2 rounded-lg">
            Manage Users
        </a>
        @endrole

    </div>
</div>

    {{-- DEBUG (VERY IMPORTANT) --}}
    <div class="bg-yellow-100 p-4 rounded text-sm">
        <strong>DEBUG:</strong><br>
        Products per Category: {{ json_encode($productsPerCategory) }} <br>
        Variants per Product: {{ json_encode($variantsPerProduct) }}
    </div>

    {{-- CHARTS --}}
    <div class="grid md:grid-cols-2 gap-6">

        <div wire:ignore class="bg-white p-6 rounded shadow">
            <h2 class="mb-4 font-bold">Products per Category</h2>
            <canvas id="productsPerCategory"></canvas>
        </div>

        <div wire:ignore class="bg-white p-6 rounded shadow">
            <h2 class="mb-4 font-bold">Variants per Product</h2>
            <canvas id="variantsPerProduct"></canvas>
        </div>

    </div>

</div>