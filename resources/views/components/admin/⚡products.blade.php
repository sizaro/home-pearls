<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use App\Services\CloudinaryImageService;

new #[Layout('layouts.admin')] class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $description = '';
    public int $category_id = 0;

    public $imageFile = null;

    public int $productId = 0;
    public bool $modalOpen = false;

    public $products = [];
    public $categories = [];

    public $imageTimestamp = null;

    public function mount()
    {
        $this->loadProducts();

        // ✅ FIX: LOAD ALL CATEGORIES (NO FILTERING)
        $this->categories = Category::orderBy('name')->get();
    }

    public function loadProducts()
    {
        $user = Auth::user();

        $query = Product::with(['category', 'creator'])->latest();

        // ✅ KEEP PRODUCT OWNERSHIP
        if (!$user->hasRole('super admin')) {
            $query->where('created_by', $user->id);
        }

        $this->products = $query->get();
    }

    public function openModal($id = null)
    {
        if ($id) {
            $product = Product::findOrFail($id);

            $this->authorize('update', $product);

            $this->productId = $product->id;
            $this->name = $product->name;
            $this->description = $product->description;
            $this->category_id = $product->category_id;
            $this->imageFile = null;
        } else {
            $this->authorize('create', Product::class);
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
        $this->productId = 0;
        $this->name = '';
        $this->description = '';
        $this->category_id = 0;
        $this->imageFile = null;
    }

    public function saveProduct()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

            // ✅ FIX: REMOVE created_by FILTER
            'category_id' => 'required|exists:categories,id',

            'imageFile' => $this->productId
                ? 'nullable|image|max:2048'
                : 'required|image|max:2048',
        ]);

        $user = Auth::user();

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $this->authorize('update', $product);

            $replacement = $this->imageFile ? $this->uploadImage() : null;
            if ($this->imageFile && ! $replacement) return;

            $previousPublicId = $product->image_public_id;

            $product->update([
                'name' => $this->name,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'slug' => Str::slug($this->name),
                'image_url' => $replacement['url'] ?? $product->image_url,
                'image_public_id' => $replacement['public_id'] ?? $product->image_public_id,
            ]);

            if ($replacement && $previousPublicId) {
                try {
                    app(CloudinaryImageService::class)->delete($previousPublicId, 'products');
                } catch (Throwable $exception) {
                    Log::warning('A replaced Home Pearls product image could not be removed from Cloudinary.', ['product_id' => $product->id, 'exception' => $exception]);
                }
            }

            $this->imageTimestamp = now()->timestamp;
        } else {

            $this->authorize('create', Product::class);

            $image = $this->uploadImage();
            if (! $image) return;

            try {
                Product::create([
                    'name' => $this->name,
                    'description' => $this->description,
                    'category_id' => $this->category_id,
                    'slug' => Str::slug($this->name),
                    'image_url' => $image['url'],
                    'image_public_id' => $image['public_id'],
                    'created_by' => $user->id,
                ]);
            } catch (Throwable $exception) {
                app(CloudinaryImageService::class)->delete($image['public_id'], 'products');
                throw $exception;
            }
        }

        $this->loadProducts();
        $this->closeModal();
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);

        $this->authorize('delete', $product);

        try {
            $product->loadMissing('variants');
            $images = $product->variants->map(fn ($variant) => [$variant->image_public_id, 'product-variants'])
                ->push([$product->image_public_id, 'products']);

            foreach ($images as [$publicId, $collection]) {
                app(CloudinaryImageService::class)->delete($publicId, $collection);
            }
        } catch (Throwable $exception) {
            report($exception);
            session()->flash('error', 'The product image could not be removed. The product was not deleted.');
            return;
        }

        $product->delete();
        $this->loadProducts();
    }

    private function uploadImage(): ?array
    {
        try {
            return app(CloudinaryImageService::class)->upload($this->imageFile, 'products');
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('imageFile', 'Image upload failed. Please try again.');
            return null;
        }
    }
};
?>

<div class="space-y-6 p-6 bg-[#F6F1EB] text-[#3B2F2A]">

    <h1 class="text-2xl font-bold mb-4">Products</h1>

    @can('create', App\Models\Product::class)
        <button wire:click="openModal"
            class="mb-4 px-4 py-2 bg-[#38BDF8] text-white rounded hover:opacity-90">
            Add New Product
        </button>
    @endcan

    <div class="bg-white shadow rounded border border-[#E7DED5]">

        <div class="flex font-bold border-b p-2 bg-[#F6F1EB] text-[#3B2F2A]">
            <div class="w-1/12">ID</div>
            <div class="w-2/12">Name</div>
            <div class="w-2/12">Category</div>

            @role('super admin')
                <div class="w-2/12">Created By</div>
            @endrole

            <div class="w-3/12">Image</div>
            <div class="w-2/12">Actions</div>
        </div>

        @foreach ($products as $product)
            <div class="flex border-b p-2 items-center text-[#3B2F2A]">

                <div class="w-1/12">{{ $product->id }}</div>
                <div class="w-2/12">{{ $product->name }}</div>
                <div class="w-2/12">{{ $product->category?->name }}</div>

                @role('super admin')
                    <div class="w-2/12">{{ $product->creator?->name }}</div>
                @endrole

                <div class="w-3/12">
                    @if ($product->image_url)
                        <img src="{{ route('products.image', $product->id) }}?t={{ $imageTimestamp ?? now()->timestamp }}"
                             class="h-12 w-12 object-cover rounded border border-[#E7DED5]">
                    @else
                        <span class="text-[#3B2F2A]/50">No Image</span>
                    @endif
                </div>

                <div class="w-2/12 flex gap-2">

                    @can('update', $product)
                        <button wire:click="openModal({{ $product->id }})"
                            class="px-3 py-1 bg-[#38BDF8] text-white rounded">
                            Edit
                        </button>
                    @endcan

                    @can('delete', $product)
                        <button wire:click="deleteProduct({{ $product->id }})"
                            class="px-3 py-1 bg-red-500 text-white rounded">
                            Delete
                        </button>
                    @endcan

                </div>
            </div>
        @endforeach

    </div>

    {{-- MODAL --}}
    @if ($modalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

            <div class="bg-[#F6F1EB] p-6 rounded shadow w-96 text-[#3B2F2A]">

                <h2 class="text-xl font-bold mb-4">
                    {{ $productId ? 'Edit Product' : 'Add Product' }}
                </h2>

                <input type="text"
                    wire:model="name"
                    class="border border-[#E7DED5] p-2 w-full mb-2 rounded">

                <textarea
                    wire:model="description"
                    class="border border-[#E7DED5] p-2 w-full mb-2 rounded">
                </textarea>

                <select wire:model="category_id"
                    class="border border-[#E7DED5] p-2 w-full mb-2 rounded">

                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <input type="file"
                    wire:model="imageFile"
                    class="border border-[#E7DED5] p-2 w-full mb-4 rounded">

                {{-- PREVIEW --}}
                @if ($imageFile)
                    <img src="{{ $imageFile->temporaryUrl() }}"
                         class="h-24 mb-4 rounded border border-[#E7DED5]">
                @elseif ($productId)
                    <img src="{{ route('products.image', $productId) }}?t={{ now()->timestamp }}"
                         class="h-24 mb-4 rounded border border-[#E7DED5]">
                @endif

                <div class="flex justify-end gap-2">

                    <button wire:click="closeModal"
                        class="px-4 py-2 bg-[#3B2F2A] text-white rounded">
                        Cancel
                    </button>

                    <button wire:click="saveProduct"
                        class="px-4 py-2 bg-[#38BDF8] text-white rounded">
                        {{ $productId ? 'Update' : 'Add' }}
                    </button>

                </div>

            </div>

        </div>
    @endif
</div>
