<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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
        $this->loadProducts();
    }

    private function loadProducts()
    {
        $user = Auth::user();

        $this->products = $user->hasRole('super admin')
            ? Product::orderBy('name')->get()
            : Product::where('created_by', $user->id)->orderBy('name')->get();
    }

    public function loadVariants()
    {
        $user = Auth::user();

        $query = ProductVariant::with(['product', 'product.creator']);

        if (!$user->hasRole('super admin')) {
            $query->whereHas('product', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }

        $this->variants = $query->latest()->get();
    }

    public function openModal($id = null)
    {
        if ($id) {
            $variant = ProductVariant::findOrFail($id);

            $this->authorize('update', $variant);

            $this->variantId = $variant->id;
            $this->product_id = $variant->product_id;
            $this->name = $variant->name;
            $this->description = $variant->description;
            $this->price = $variant->price;
            $this->stock = $variant->stock;
            $this->sku = $variant->sku;

            $this->imageFile = null;
        } else {
            $this->authorize('create', ProductVariant::class);
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
        return 'HPA-' .
            Str::upper(Str::slug($productName, '')) . '-' .
            Str::upper(Str::slug($variantName, '')) . '-' .
            rand(1000, 9999);
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

        if (!Auth::user()->hasRole('super admin')) {
            abort_if($product->created_by !== Auth::id(), 403);
        }

        if (!$this->sku) {
            $this->sku = $this->generateSku($product->name, $this->name);
        }

        if ($this->variantId) {

            $variant = ProductVariant::findOrFail($this->variantId);

            $this->authorize('update', $variant);

            $image_path = $variant->image_url;

            if ($this->imageFile) {

                if ($image_path && Storage::exists('private/product-variants/' . $image_path)) {
                    Storage::delete('private/product-variants/' . $image_path);
                }

                $filename = time() . '_' . $this->imageFile->getClientOriginalName();
                $this->imageFile->storeAs('private/product-variants', $filename);

                $image_path = $filename;
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

            $this->authorize('create', ProductVariant::class);

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

        $this->authorize('delete', $variant);

        if ($variant->image_url &&
            Storage::exists('private/product-variants/' . $variant->image_url)) {
            Storage::delete('private/product-variants/' . $variant->image_url);
        }

        $variant->delete();
        $this->loadVariants();
    }
};
?>

<div class="space-y-6 p-6">

    <h1 class="text-2xl font-bold mb-4">Product Variants</h1>

    @can('create', App\Models\ProductVariant::class)
        <button wire:click="openModal"
            class="mb-4 px-4 py-2 bg-green-500 text-white rounded">
            Add New Variant
        </button>
    @endcan

    <div class="bg-white shadow rounded">
        <div class="flex font-bold border-b p-2">
            <div class="w-1/12">ID</div>
            <div class="w-2/12">Name</div>
            <div class="w-2/12">Product</div>

            @role('super admin')
                <div class="w-2/12">Created By</div>
            @endrole

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

                @role('super admin')
                    <div class="w-2/12">{{ $variant->product?->creator?->name }}</div>
                @endrole

                <div class="w-2/12">${{ number_format($variant->price, 2) }}</div>
                <div class="w-1/12">{{ $variant->stock }}</div>

                <div class="w-2/12">
                    @if ($variant->image_url)
                        <img src="{{ route('product-variants.image', $variant->id) }}?t={{ time() }}"
                             class="h-12 w-12 object-cover rounded">
                    @endif
                </div>

                <div class="w-2/12 flex gap-2">
                    @can('update', $variant)
                        <button wire:click="openModal({{ $variant->id }})"
                            class="px-3 py-1 bg-yellow-500 text-white rounded">
                            Edit
                        </button>
                    @endcan

                    @can('delete', $variant)
                        <button wire:click="deleteVariant({{ $variant->id }})"
                            class="px-3 py-1 bg-red-500 text-white rounded">
                            Delete
                        </button>
                    @endcan
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODAL (FIXED IMAGE PREVIEW) --}}
    @if ($modalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded shadow w-96">

                <h2 class="text-xl font-bold mb-4">
                    {{ $variantId ? 'Edit Variant' : 'Add Variant' }}
                </h2>

                <select wire:model="product_id" class="border p-2 w-full mb-2">
                    <option value="">Select Product</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                    @endforeach
                </select>

                <input type="text" wire:model="name" class="border p-2 w-full mb-2">
                <textarea wire:model="description" class="border p-2 w-full mb-2"></textarea>

                <input type="number" wire:model="price" class="border p-2 w-full mb-2">
                <input type="number" wire:model="stock" class="border p-2 w-full mb-2">

                <input type="text" wire:model="sku" disabled class="border p-2 w-full mb-2">

                <input type="file" wire:model="imageFile" class="border p-2 w-full mb-4">

                {{-- FIXED PREVIEW --}}
                @if ($imageFile)
                    <img src="{{ $imageFile->temporaryUrl() }}" class="h-24 mb-4 rounded">
                @elseif ($variantId)
                    <img src="{{ route('product-variants.image', $variantId) }}?t={{ time() }}"
                         class="h-24 mb-4 rounded">
                @endif

                <div class="flex justify-end gap-2">
                    <button wire:click="closeModal"
                        class="px-4 py-2 bg-gray-500 text-white rounded">
                        Cancel
                    </button>

                    <button wire:click="saveVariant"
                        class="px-4 py-2 bg-green-500 text-white rounded">
                        {{ $variantId ? 'Update' : 'Add' }}
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>