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

    protected $listeners = ['refreshDashboard' => '$refresh'];

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

    $this->productsPerCategory = Category::withCount('products')
        ->get()
        ->pluck('products_count', 'name')
        ->toArray();

    $this->variantsPerProduct = Product::withCount('variants')
        ->get()
        ->pluck('variants_count', 'name')
        ->toArray();

    // SEND TO JS
   $this->dispatch('chartsUpdated', [
    'productsPerCategory' => $this->productsPerCategory,
    'variantsPerProduct' => $this->variantsPerProduct,
]);
}

    public function getListeners(): array
    {
        return [
            "echo-private:admin-orders,OrderUpdated" => 'loadData'
        ];
    }
};
?>

<div class="space-y-8 p-6 bg-[#F6F1EB] min-h-screen text-[#3B2F2A]">

    {{-- TITLE --}}
    <div>
        <h1 class="text-3xl font-bold text-[#3B2F2A]">Admin Dashboard</h1>
        <p class="text-[#3B2F2A]/60">Manage your store and track statistics</p>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded-xl shadow border border-[#3B2F2A]/10">
            <h2 class="text-[#3B2F2A]/60">New Orders</h2>
            <p class="text-3xl font-bold text-[#38BDF8]">{{ $newOrdersCount }}</p>

            <a href="{{ route('admin.orders') }}"
               class="mt-3 inline-block bg-[#38BDF8] text-white px-4 py-2 rounded-lg hover:opacity-90">
                View Orders
            </a>
        </div>

        <div class="bg-white p-6 rounded-xl shadow border border-[#3B2F2A]/10">
            <h2 class="text-[#3B2F2A]/60">Categories</h2>
            <p class="text-3xl font-bold text-[#3B2F2A]">{{ $categoriesCount }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow border border-[#3B2F2A]/10">
            <h2 class="text-[#3B2F2A]/60">Products</h2>
            <p class="text-3xl font-bold text-[#3B2F2A]">{{ $productsCount }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow border border-[#3B2F2A]/10">
            <h2 class="text-[#3B2F2A]/60">Variants</h2>
            <p class="text-3xl font-bold text-[#3B2F2A]">{{ $variantsCount }}</p>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="bg-white p-6 rounded-xl shadow border border-[#3B2F2A]/10">
        <h2 class="text-xl font-bold text-[#3B2F2A] mb-4">Quick Actions</h2>

        <div class="flex flex-wrap gap-4">

            <a href="{{ route('admin.categories') }}"
               class="bg-[#3B2F2A] text-white px-4 py-2 rounded-lg hover:opacity-90">
                Manage Categories
            </a>

            <a href="{{ route('admin.products') }}"
               class="bg-[#38BDF8] text-white px-4 py-2 rounded-lg hover:opacity-90">
                Manage Products
            </a>

            <a href="{{ route('admin.product-variants') }}"
               class="bg-[#8B5E3C] text-white px-4 py-2 rounded-lg hover:opacity-90">
                Manage Variants
            </a>

            @role('super admin')
            <a href="{{ route('admin.users') }}"
               class="bg-[#E7DED5] text-[#3B2F2A] px-4 py-2 rounded-lg hover:opacity-90">
                Manage Users
            </a>
            @endrole

        </div>
    </div>

    {{-- CHARTS --}}
        <div wire:ignore class="bg-white p-6 rounded-xl shadow border border-[#3B2F2A]/10">
    <h2 class="text-xl font-bold mb-4 text-[#3B2F2A]">Products per Category</h2>
    <canvas id="productsPerCategory"></canvas>
</div>

<div wire:ignore class="bg-white p-6 rounded-xl shadow border border-[#3B2F2A]/10">
    <h2 class="text-xl font-bold mb-4 text-[#3B2F2A]">Variants per Product</h2>
    <canvas id="variantsPerProduct"></canvas>
</div>
</div>