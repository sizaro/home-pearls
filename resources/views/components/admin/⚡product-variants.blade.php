<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;

new #[Layout('layouts.admin')] class extends Component
{
    use WithFileUploads;

    public int $product_id = 0;
    public string $name = '';
    public string $description = '';
    public float $price = 0;
    public int $stock = 0;
    public string $sku = '';
    public $imageFile = null;
    public int $variantId = 0;
    public bool $modalOpen = false;

    public $variants = [];
    public $products = [];

    public function mount()
    {
        $this->loadVariants();
        $this->products = Product::orderBy('name')->get();
    }

    public function loadVariants()
    {
        $this->variants = ProductVariant::with('product')->orderBy('id', 'desc')->get();
    }

    public function openModal($id = null)
    {
        if ($id) {
            $variant = ProductVariant::findOrFail($id);
            $this->variantId = $variant->id;
            $this->product_id = $variant->product_id;
            $this->name = $variant->name;
            $this->description = $variant->description;
            $this->price = $variant->price;
            $this->stock = $variant->stock;
            $this->sku = $variant->sku;
            $this->imageFile = null;
        } else {
            $this->resetFields();
        }

        $this->modalOpen = true;
    }

    public function closeModal()
    {
        $this->modalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->variantId = 0;
        $this->product_id = 0;
        $this->name = '';
        $this->description = '';
        $this->price = 0;
        $this->stock = 0;
        $this->sku = '';
        $this->imageFile = null;
    }

    private function generateSku($productName, $variantName)
    {
        $slugProduct = Str::upper(Str::slug($productName, ''));
        $slugVariant = Str::upper(Str::slug($variantName, ''));
        $random = rand(1000, 9999);
        return "HPA-{$slugProduct}-{$slugVariant}-{$random}";
    }

    public function saveVariant()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255',
            'imageFile' => $this->variantId ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        $product = Product::findOrFail($this->product_id);

        // Generate SKU automatically if empty
        if (!$this->sku) {
            $this->sku = $this->generateSku($product->name, $this->name);
        }

        if ($this->variantId) {
            $variant = ProductVariant::findOrFail($this->variantId);

            // Delete old image if replaced
            if ($this->imageFile) {
                if ($variant->image_url && Storage::exists('private/product-variants/' . $variant->image_url)) {
                    Storage::delete('private/product-variants/' . $variant->image_url);
                }
                $filename = time() . '_' . $this->imageFile->getClientOriginalName();
                $this->imageFile->storeAs('private/product-variants', $filename);
                $image_path = $filename;
            } else {
                $image_path = $variant->image_url;
            }

            $variant->update([
                'product_id' => $this->product_id,
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'stock' => $this->stock,
                'sku' => $this->sku,
                'image_url' => $image_path,
            ]);
        } else {
            $filename = time() . '_' . $this->imageFile->getClientOriginalName();
            $this->imageFile->storeAs('private/product-variants', $filename);

            ProductVariant::create([
                'product_id' => $this->product_id,
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'stock' => $this->stock,
                'sku' => $this->sku,
                'image_url' => $filename,
            ]);
        }

        $this->loadVariants();
        $this->closeModal();
    }

    public function deleteVariant($id)
    {
        $variant = ProductVariant::findOrFail($id);

        if ($variant->image_url && Storage::exists('private/product-variants/' . $variant->image_url)) {
            Storage::delete('private/product-variants/' . $variant->image_url);
        }

        $variant->delete();
        $this->loadVariants();
    }
}
?>

<div class="space-y-6 p-6">
    <h1 class="text-2xl font-bold mb-4">Product Variants</h1>

    <button wire:click="openModal"
        class="mb-4 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
        Add New Variant
    </button>

    {{-- Variants Table --}}
    <div class="bg-white shadow rounded">
        <div class="flex font-bold border-b p-2">
            <div class="w-1/12">ID</div>
            <div class="w-2/12">Name</div>
            <div class="w-2/12">Product</div>
            <div class="w-2/12">Price</div>
            <div class="w-1/12">Stock</div>
            <div class="w-2/12">Image</div>
            <div class="w-2/12">Actions</div>
        </div>

        @foreach ($variants as $variant)
            <div class="flex border-b p-2 items-center">
                <div class="w-1/12">{{ $variant->id }}</div>
                <div class="w-2/12">{{ $variant->name }}</div>
                <div class="w-2/12">{{ $variant->product?->name }}</div>
                <div class="w-2/12">${{ number_format($variant->price, 2) }}</div>
                <div class="w-1/12">{{ $variant->stock }}</div>
                <div class="w-2/12">
                    @if ($variant->image_url)
                        <img src="{{ route('product-variants.image', $variant->id) }}?{{ time() }}"
                             class="h-12 w-12 object-cover rounded">
                    @else
                        <span class="text-gray-400">No Image</span>
                    @endif
                </div>
                <div class="w-2/12 flex gap-2">
                    <button wire:click="openModal({{ $variant->id }})"
                        class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                        Edit
                    </button>
                    <button wire:click="deleteVariant({{ $variant->id }})"
                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                        Delete
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modal --}}
    @if ($modalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded shadow w-96 overflow-y-auto max-h-[80vh]">
                <h2 class="text-xl font-bold mb-4">
                    {{ $variantId ? 'Edit Variant' : 'Add Variant' }}
                </h2>

                <select wire:model="product_id" class="border p-2 w-full mb-2">
                    <option value="">Select Product</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                    @endforeach
                </select>

                <input type="text" wire:model="name" placeholder="Variant Name"
                       class="border p-2 w-full mb-2">

                <textarea wire:model="description" placeholder="Variant Description"
                          class="border p-2 w-full mb-2"></textarea>

                <label>Price
                    <input type="number" step="0.01" wire:model="price"
                       placeholder="Price" class="border p-2 w-full mb-2">
                </label>
                
                <label>Stock
                    <input type="number" wire:model="stock"
                       placeholder="Stock" class="border p-2 w-full mb-2">
                </label>

                <input type="text" wire:model="sku" disabled
                       placeholder="SKU" class="border p-2 w-full mb-2">

                <input type="file" wire:model="imageFile" class="border p-2 w-full mb-4">

                @if ($imageFile)
                    <img src="{{ $imageFile->temporaryUrl() }}" class="h-24 mb-4 rounded">
                @elseif ($variantId && $variants->find($variantId)?->image_url)
                    <img src="{{ route('product-variants.image', $variantId) }}?{{ time() }}"
                         class="h-24 mb-4 rounded">
                @endif

                <div class="flex justify-end gap-2">
                    <button wire:click="closeModal"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Cancel
                    </button>
                    <button wire:click="saveVariant"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        {{ $variantId ? 'Update' : 'Add' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>