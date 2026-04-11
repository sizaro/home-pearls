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
        // Stats
        $this->categoriesCount = Category::count();
        $this->productsCount   = Product::count();
        $this->variantsCount   = ProductVariant::count();

        // Orders
        $this->newOrdersCount = Order::where('status', 'pending')->count();

        // Charts
        $this->productsPerCategory = Category::withCount('products')
            ->get()
            ->pluck('products_count', 'name')
            ->toArray();

        $this->variantsPerProduct = Product::withCount('variants')
            ->get()
            ->pluck('variants_count', 'name')
            ->toArray();
    }

    // Optional: Live refresh every X seconds
    public function getListeners(): array
    {
        return [
            "echo-private:admin-orders,OrderUpdated" => 'loadData'
        ];
    }
}
?>

<div class="space-y-8 p-6">

    {{-- PAGE TITLE --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-500">Manage your store and track statistics</p>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow">
    <h2 class="text-gray-500">New Orders</h2>
    <p class="text-3xl font-bold">{{ $newOrdersCount }}</p>
    <a href="{{ route('admin.orders') }}"
       class="mt-2 inline-block bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
       View Orders
    </a>
</div>
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

            @role('super admin')
            <a href="{{ route('admin.users') }}"
            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Manage Users
            </a>
            @endrole
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Products per Category (Pie) --}}
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Products per Category</h2>
            <canvas id="productsPerCategory" class="w-full h-64"></canvas>
        </div>

        {{-- Variants per Product (Bar) --}}
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Variants per Product</h2>
            <canvas id="variantsPerProduct" class="w-full h-64"></canvas>
        </div>

    </div>
</div>

{{-- CHARTS SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let pieChart, barChart;

document.addEventListener('livewire:load', function () {
    updateCharts(@json($productsPerCategory), @json($variantsPerProduct));

    Livewire.hook('message.processed', (message, component) => {
        updateCharts(@this.productsPerCategory, @this.variantsPerProduct);
    });
});

function updateCharts(productsPerCategory, variantsPerProduct) {
    const pieCtx = document.getElementById('productsPerCategory').getContext('2d');
    if(pieChart) pieChart.destroy();
    pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(productsPerCategory),
            datasets: [{
                data: Object.values(productsPerCategory),
                backgroundColor: ['#f87171', '#60a5fa', '#34d399', '#a78bfa'],
            }]
        }
    });

    const barCtx = document.getElementById('variantsPerProduct').getContext('2d');
    if(barChart) barChart.destroy();
    barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(variantsPerProduct),
            datasets: [{
                label: 'Number of Variants',
                data: Object.values(variantsPerProduct),
                backgroundColor: '#60a5fa'
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
}

</script>