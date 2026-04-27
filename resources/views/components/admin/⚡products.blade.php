<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

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

    $user = Auth::user();

    $query = Category::orderBy('name');

    if (!$user->hasRole('super admin')) {
        $query->where('created_by', $user->id);
    }

    $this->categories = $query->get();
}

    public function loadProducts()
    {
        $user = Auth::user();

        $query = Product::with(['category', 'creator'])->latest();

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
            'category_id' => [
                    'required',
                    Rule::exists('categories', 'id')->where(function ($query) {
                        $user = Auth::user();

                        if (!$user->hasRole('super admin')) {
                            $query->where('created_by', $user->id);
                        }
                    }),
                ],
            'imageFile' => $this->productId
                ? 'nullable|image|max:2048'
                : 'required|image|max:2048',
        ]);

        $user = Auth::user();

        // =====================
        // UPDATE
        // =====================
        if ($this->productId) {

            $product = Product::findOrFail($this->productId);

            $this->authorize('update', $product);

            $image_path = $product->image_url;

            if ($this->imageFile) {

                if ($product->image_url &&
                    Storage::exists('private/products/' . $product->image_url)) {
                    Storage::delete('private/products/' . $product->image_url);
                }

                $filename = time() . '_' . $this->imageFile->getClientOriginalName();
                $this->imageFile->storeAs('private/products', $filename);

                $image_path = $filename;
                $this->imageTimestamp = now()->timestamp;
            }

            $product->update([
                'name' => $this->name,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'slug' => Str::slug($this->name),
                'image_url' => $image_path,
            ]);

        } 
        // =====================
        // CREATE
        // =====================
        else {

            $this->authorize('create', Product::class);

            $filename = time() . '_' . $this->imageFile->getClientOriginalName();
            $this->imageFile->storeAs('private/products', $filename);

            Product::create([
                'name' => $this->name,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'slug' => Str::slug($this->name),
                'image_url' => $filename,
                'created_by' => $user->id,
            ]);
        }

        $this->loadProducts();
        $this->closeModal();
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);

        $this->authorize('delete', $product);

        if ($product->image_url &&
            Storage::exists('private/products/' . $product->image_url)) {
            Storage::delete('private/products/' . $product->image_url);
        }

        $product->delete();
        $this->loadProducts();
    }
};
?>

<div class="space-y-6 p-6">

    <h1 class="text-2xl font-bold mb-4">Products</h1>

    @can('create', App\Models\Product::class)
        <button wire:click="openModal"
            class="mb-4 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
            Add New Product
        </button>
    @endcan

    <div class="bg-white shadow rounded">
        <div class="flex font-bold border-b p-2">
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
            <div class="flex border-b p-2 items-center">
                <div class="w-1/12">{{ $product->id }}</div>
                <div class="w-2/12">{{ $product->name }}</div>
                <div class="w-2/12">{{ $product->category?->name }}</div>

                @role('super admin')
                    <div class="w-2/12">{{ $product->creator?->name }}</div>
                @endrole

                <div class="w-3/12">
                    @if ($product->image_url)
                        <img src="{{ route('products.image', $product->id) }}?t={{ $imageTimestamp ?? now()->timestamp }}"
                             class="h-12 w-12 object-cover rounded">
                    @else
                        <span class="text-gray-400">No Image</span>
                    @endif
                </div>

                <div class="w-2/12 flex gap-2">
                    @can('update', $product)
                        <button wire:click="openModal({{ $product->id }})"
                            class="px-3 py-1 bg-yellow-500 text-white rounded">
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
            <div class="bg-white p-6 rounded shadow w-96">

                <h2 class="text-xl font-bold mb-4">
                    {{ $productId ? 'Edit Product' : 'Add Product' }}
                </h2>

                <input type="text" wire:model="name" class="border p-2 w-full mb-2" placeholder="Name">
                <textarea wire:model="description" class="border p-2 w-full mb-2" placeholder="Description"></textarea>

                <select wire:model="category_id" class="border p-2 w-full mb-2">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <input type="file" wire:model="imageFile" class="border p-2 w-full mb-4">

                {{-- FIXED PREVIEW --}}
               @elseif ($productId)
    <img src="{{ route('products.image', $productId) }}?t={{ now()->timestamp }}"
         class="h-24 mb-4 rounded">
@endif

                <div class="flex justify-end gap-2">
                    <button wire:click="closeModal"
                        class="px-4 py-2 bg-gray-500 text-white rounded">
                        Cancel
                    </button>

                    <button wire:click="saveProduct"
                        class="px-4 py-2 bg-green-500 text-white rounded">
                        {{ $productId ? 'Update' : 'Add' }}
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>