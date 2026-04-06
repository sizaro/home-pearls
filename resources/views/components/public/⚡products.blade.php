<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;

new #[Layout('layouts.products')] class extends Component
{
    public string $search = '';
    public array $products = [];

    public function mount()
    {
        // Get search string from URL
        $this->search = request()->query('search', '');
        
        if ($this->search) {
            // Use case-insensitive partial match
            $term = strtolower($this->search);

            // Fetch products whose name contains the search term
            $this->products = Product::whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                ->with('category') // eager-load category
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'category' => $product->category ? $product->category->name : null,
                        'image_url' => $product->image_url,
                    ];
                })
                ->toArray();
        }
    }
};
?>

<div class="max-w-6xl mx-auto py-6 space-y-6">

    <h1 class="text-2xl font-bold mb-4">
        Search Results for "{{ $search }}"
    </h1>

    @if(empty($products))
        <p class="text-gray-500">No products found.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                <a href="{{ route('products.show', $product['id']) }}"
                   class="block bg-white rounded shadow hover:shadow-lg transition overflow-hidden group">
                   
                    <div class="relative h-48 md:h-56 lg:h-64 overflow-hidden">
                        <img
                            src="{{ $product['image_url'] 
                                ? route('products.image', ['id' => $product['id']]) 
                                : 'https://via.placeholder.com/400x300?text=No+Image' }}"
                            class="w-full h-48 object-cover"
                        />
                    </div>

                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800">{{ $product['name'] }}</h3>
                        @if($product['category'])
                            <p class="text-gray-500 text-sm mt-1 capitalize">{{ $product['category'] }}</p>
                        @endif
                        <span class="mt-3 inline-block bg-yellow-500 text-black px-3 py-1 rounded font-medium hover:bg-yellow-400">
                            View Product
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>