<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component
{
    // Stats
    public int $categoriesCount = 0;
    public int $productsCount = 0;
    public int $variantsCount = 0;

    // Chart data (mocked)
    public array $productsPerCategory = [];
    public array $variantsPerProduct = [];

    public function mount()
    {
        // Mocked counts
        $this->categoriesCount = 4;
        $this->productsCount = 10;
        $this->variantsCount = 20;

        // Mocked chart data
        $this->productsPerCategory = [
            'Beds' => 4,
            'Sofas' => 3,
            'Tables' => 2,
            'Chairs' => 1
        ];

        $this->variantsPerProduct = [
            'King Bed' => 3,
            'Queen Bed' => 2,
            'Sofa Set' => 4,
            'Coffee Table' => 2,
            'Dining Chair' => 1
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
document.addEventListener('livewire:load', function () {

    // Products per Category (Pie)
    const ctxPie = document.getElementById('productsPerCategory').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: @json(array_keys($productsPerCategory)),
            datasets: [{
                data: @json(array_values($productsPerCategory)),
                backgroundColor: ['#f87171', '#60a5fa', '#34d399', '#a78bfa'],
            }]
        }
    });

    // Variants per Product (Bar)
    const ctxBar = document.getElementById('variantsPerProduct').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: @json(array_keys($variantsPerProduct)),
            datasets: [{
                label: 'Number of Variants',
                data: @json(array_values($variantsPerProduct)),
                backgroundColor: '#60a5fa'
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

});
</script>