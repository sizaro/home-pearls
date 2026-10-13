<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;

new #[Layout('layouts.products')] class extends Component
{
    public string $search = '';
    public array $products = [];

    public function mount()
    {
        $this->search = request()->query('search', '');
    }

    public function getFilteredProductsProperty()
    {
        $term = strtolower(trim($this->search));

        if (!$term) return [];

        $termWords = explode(' ', $term);

        $allProducts = Product::with('category')->latest()->get();

        $results = [];

        foreach ($allProducts as $product) {
            $productName = strtolower($product->name);

            foreach ($termWords as $word) {
                if (str_contains($productName, $word)) {
                    $results[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'category' => $product->category?->name ?? '',
                        'slug' => $product->category?->slug ?? '',
                        'image_url' => $product->image_url,
                        'description' => $product->short_description,
                    ];
                    break;
                }
            }
        }

        return $results;
    }
};
?>

<div class="space-y-6 px-4 py-6 text-[#3B2F2A]">

    <h1 class="text-2xl font-bold mb-6 text-[#8B5E3C]">
        Results for "{{ $search }}"
    </h1>

    @if(empty($this->filteredProducts))
        <p class="text-[#3B2F2A]/60">No products found.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach($this->filteredProducts as $product)
                <a href="{{ route('products.show', $product['id']) }}"
                   class="block bg-[#F5F1ED] border border-[#8B5E3C]/20 rounded-xl shadow-sm hover:shadow-md transition overflow-hidden">

                    <img
                        src="{{ $product['image_url'] 
                            ? route('products.image', ['id' => $product['id']])
                            : 'https://via.placeholder.com/400x300?text=No+Image' }}"
                        class="w-full h-48 object-cover"
                    >

                    <div class="p-4">

                        <h3 class="font-semibold text-[#3B2F2A]">
                            {{ $product['name'] }}
                        </h3>

                        <p class="text-[#3B2F2A]/60 text-sm">
                            {{ $product['category'] }}
                        </p>

                        <span class="mt-3 inline-block text-[#38BDF8] font-medium">
                            View Product
                        </span>

                    </div>
                </a>
            @endforeach

        </div>
    @endif

</div>