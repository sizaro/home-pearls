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

       $this->variantsPerProduct = Product::with('variants')
    ->get()
    ->mapWithKeys(function ($product) {
        return [
            $product->name => $product->variants->sum('stock')
        ];
    })
    ->toArray();

        // ✅ VISITS (NOW CORRECT)
   $this->visitsDaily = Visit::selectRaw('DATE(created_at) as date, COUNT(*) as total')
    ->groupBy('date')
    ->pluck('total', 'date')
    ->toArray();

$this->visitsWeekly = Visit::selectRaw("TO_CHAR(created_at, 'IYYY-IW') as week, COUNT(*) as total")
    ->groupBy('week')
    ->pluck('total', 'week')
    ->toArray();

$this->visitsMonthly = Visit::selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, COUNT(*) as total")
    ->groupBy('month')
    ->pluck('total', 'month')
    ->toArray();

    $this->dispatch('chartsUpdated', [
        'productsPerCategory' => $this->productsPerCategory,
        'variantsPerProduct' => $this->variantsPerProduct,

        // send visits too
        'visitsDaily' => $this->visitsDaily,
        'visitsWeekly' => $this->visitsWeekly,
        'visitsMonthly' => $this->visitsMonthly,
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

    {{-- CHARTS --}}
    <div class="grid md:grid-cols-2 gap-6">

        <div wire:ignore class="bg-white p-6 rounded shadow w-55 h-65">
            <h2 class="mb-4 font-bold">Products per Category</h2>
            <canvas id="productsPerCategory"></canvas>
        </div>

        <div wire:ignore class="bg-white p-6 rounded shadow h-65">
            <h2 class="mb-4 font-bold">Variants per Product</h2>
            <canvas id="variantsPerProduct"></canvas>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
</div>
    </div>
<div class="flex flex-col md:flex-wrap w-full h-30 p-6 gap-5">
    <div wire:ignore class="bg-white p-6 rounded shadow">
        <h2 class="mb-4 font-bold">Daily Visitors</h2>
        <canvas id="visitsDailyChart"></canvas>
    </div>

    <div wire:ignore class="bg-white p-6 rounded shadow">
        <h2 class="mb-4 font-bold">Weekly Visitors</h2>
        <canvas id="visitsWeeklyChart"></canvas>
    </div>

    <div wire:ignore class="bg-white p-6 rounded shadow">
        <h2 class="mb-4 font-bold">Monthly Visitors</h2>
        <canvas id="visitsMonthlyChart"></canvas>
    </div>

</div>
</div>