<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\Visit;

new #[Layout('layouts.admin')] class extends Component
{
    public array $visitsDaily = [];
    public array $visitsWeekly = [];
    public array $visitsMonthly = [];

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
        $this->productsCount = Product::count();
        $this->variantsCount = ProductVariant::count();

        $this->newOrdersCount = Order::where('status', 'pending')->count();

        // PRODUCTS PER CATEGORY
        $this->productsPerCategory = Category::withCount('products')
            ->get()
            ->mapWithKeys(function ($cat) {
                return [
                    $cat->name ?? 'Unknown' => $cat->products_count
                ];
            })
            ->toArray();

        // VARIANTS PER PRODUCT
        $this->variantsPerProduct = Product::with('variants')
            ->get()
            ->mapWithKeys(function ($product) {
                return [
                    $product->name => $product->variants->sum('stock')
                ];
            })
            ->toArray();

        // VISITS DAILY
        $this->visitsDaily = Visit::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // VISITS WEEKLY
        $this->visitsWeekly = Visit::selectRaw("TO_CHAR(created_at, 'IYYY-IW') as week, COUNT(*) as total")
            ->groupBy('week')
            ->pluck('total', 'week')
            ->toArray();

        // VISITS MONTHLY
        $this->visitsMonthly = Visit::selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, COUNT(*) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $this->dispatch('chartsUpdated', [
            'productsPerCategory' => $this->productsPerCategory,
            'variantsPerProduct' => $this->variantsPerProduct,
            'visitsDaily' => $this->visitsDaily,
            'visitsWeekly' => $this->visitsWeekly,
            'visitsMonthly' => $this->visitsMonthly,
        ]);
    }
};
?>

<div class="p-6 bg-[#F6F1EB] min-h-screen text-[#3B2F2A] space-y-8">

    {{-- PAGE TITLE --}}
    <div>
        <h1 class="text-3xl font-bold">
            Admin Dashboard
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Manage your store, products, users, and orders.
        </p>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- ORDERS --}}
        <a href="{{ route('admin.orders') }}"
           class="bg-white p-5 rounded-xl shadow hover:shadow-lg transition block border border-transparent hover:border-[#38BDF8]">

            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm text-gray-500">
                        Pending Orders
                    </h2>

                    <p class="text-3xl font-bold text-[#38BDF8] mt-2">
                        {{ $newOrdersCount }}
                    </p>
                </div>

                <div class="text-[#38BDF8] text-3xl">
                    📦
                </div>
            </div>

            <p class="mt-4 text-sm text-[#38BDF8] font-semibold">
                Manage Orders →
            </p>
        </a>

        {{-- CATEGORIES --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <h2 class="text-sm text-gray-500">
                Categories
            </h2>

            <p class="text-3xl font-bold mt-2">
                {{ $categoriesCount }}
            </p>
        </div>

        {{-- PRODUCTS --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <h2 class="text-sm text-gray-500">
                Products
            </h2>

            <p class="text-3xl font-bold mt-2">
                {{ $productsCount }}
            </p>
        </div>

        {{-- VARIANTS --}}
        <div class="bg-white p-5 rounded-xl shadow">
            <h2 class="text-sm text-gray-500">
                Variants
            </h2>

            <p class="text-3xl font-bold mt-2">
                {{ $variantsCount }}
            </p>
        </div>

    </div>

    {{-- QUICK ACTIONS --}}
    <div class="bg-white p-6 rounded-xl shadow border border-[#3B2F2A]/10">

        <h2 class="text-xl font-bold text-[#3B2F2A] mb-5">
            Quick Actions
        </h2>

        <div class="flex flex-wrap gap-4">

            <a href="{{ route('admin.categories') }}"
               class="bg-[#3B2F2A] hover:opacity-90 transition text-white px-5 py-3 rounded-lg font-semibold">
                Manage Categories
            </a>

            <a href="{{ route('admin.products') }}"
               class="bg-[#38BDF8] hover:opacity-90 transition text-white px-5 py-3 rounded-lg font-semibold">
                Manage Products
            </a>

            <a href="{{ route('admin.product-variants') }}"
               class="bg-[#8B5E3C] hover:opacity-90 transition text-white px-5 py-3 rounded-lg font-semibold">
                Manage Variants
            </a>

            {{-- NEW --}}
            <a href="{{ route('admin.orders') }}"
               class="bg-green-600 hover:bg-green-700 transition text-white px-5 py-3 rounded-lg font-semibold">
                Manage Orders
            </a>

            @role('super admin')
            <a href="{{ route('admin.users') }}"
               class="bg-[#E7DED5] hover:bg-[#d8cec5] transition text-[#3B2F2A] px-5 py-3 rounded-lg font-semibold">
                Manage Users
            </a>
            @endrole

        </div>
    </div>

    {{-- MAIN CHARTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div wire:ignore class="bg-white p-6 rounded-xl shadow">
            <h2 class="mb-4 font-bold text-lg">
                Products per Category
            </h2>

            <canvas id="productsPerCategory"></canvas>
        </div>

        <div wire:ignore class="bg-white p-6 rounded-xl shadow">
            <h2 class="mb-4 font-bold text-lg">
                Variants per Product
            </h2>

            <canvas id="variantsPerProduct"></canvas>
        </div>

    </div>

    {{-- VISITS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div wire:ignore class="bg-white p-6 rounded-xl shadow">
            <h2 class="mb-4 font-bold text-lg">
                Daily Visitors
            </h2>

            <canvas id="visitsDailyChart"></canvas>
        </div>

        <div wire:ignore class="bg-white p-6 rounded-xl shadow">
            <h2 class="mb-4 font-bold text-lg">
                Weekly Visitors
            </h2>

            <canvas id="visitsWeeklyChart"></canvas>
        </div>

        <div wire:ignore class="bg-white p-6 rounded-xl shadow">
            <h2 class="mb-4 font-bold text-lg">
                Monthly Visitors
            </h2>

            <canvas id="visitsMonthlyChart"></canvas>
        </div>

    </div>

</div>