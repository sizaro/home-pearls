<?php

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

new #[Layout('layouts.admin')] class extends Component
{
    public string $name = '';
    public int $categoryId = 0;
    public bool $modalOpen = false;

    public $categories = [];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $user = Auth::user();

        // Super admin sees all
        if ($user->hasRole('super admin')) {
            $this->categories = Category::orderBy('id', 'desc')->get();
        } else {
            // Admin sees only their own
            $this->categories = Category::where('created_by', $user->id)
                ->orderBy('id', 'desc')
                ->get();
        }
    }

    public function openModal($id = null)
    {
        if ($id) {
            $category = Category::findOrFail($id);

            // ✅ POLICY CHECK
            $this->authorize('update', $category);

            $this->categoryId = $category->id;
            $this->name = $category->name;
        } else {
            // ✅ POLICY CHECK (create)
            $this->authorize('create', Category::class);

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
        $this->categoryId = 0;
        $this->name = '';
    }

    public function saveCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // =========================
        // UPDATE
        // =========================
        if ($this->categoryId) {

            $category = Category::findOrFail($this->categoryId);

            // ✅ POLICY CHECK
            $this->authorize('update', $category);

            $category->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
            ]);
        }

        // =========================
        // CREATE
        // =========================
        else {

            // ✅ POLICY CHECK
            $this->authorize('create', Category::class);

            Category::create([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'created_by' => $user->id,
            ]);
        }

        $this->closeModal();
        $this->loadCategories();
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);

        // ✅ POLICY CHECK
        $this->authorize('delete', $category);

        $category->delete();
        $this->loadCategories();
    }
};
?>

<div class="space-y-6 p-6">

    <h1 class="text-2xl font-bold mb-4">Categories</h1>

    {{-- Add Button --}}
    <button wire:click="openModal"
        class="mb-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
        Add New Category
    </button>

    {{-- Table --}}
    <div class="bg-white shadow rounded">
        <div class="flex font-bold border-b p-2">
            <div class="w-1/6">ID</div>
            <div class="w-3/6">Name</div>
            <div class="w-2/6">Actions</div>
        </div>

        @foreach ($categories as $category)
            <div class="flex border-b p-2 items-center">
                <div class="w-1/6">{{ $category->id }}</div>
                <div class="w-3/6">{{ $category->name }}</div>

                <div class="w-2/6 flex gap-2">
                    @can('update', $category)
                        <button wire:click="openModal({{ $category->id }})"
                            class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                            Edit
                        </button>
                    @endcan

                    @can('delete', $category)
                        <button wire:click="deleteCategory({{ $category->id }})"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                            Delete
                        </button>
                    @endcan
                </div>
            </div>
        @endforeach
    </div>

    {{-- Modal --}}
    @if ($modalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded shadow w-96">

                <h2 class="text-xl font-bold mb-4">
                    {{ $categoryId ? 'Edit Category' : 'Add Category' }}
                </h2>

                <input type="text" wire:model="name"
                    class="border p-2 w-full mb-4"
                    placeholder="Category Name">

                <div class="flex justify-end gap-2">
                    <button wire:click="closeModal"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Cancel
                    </button>

                    <button wire:click="saveCategory"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        {{ $categoryId ? 'Update' : 'Add' }}
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>