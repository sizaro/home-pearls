<?php

use Livewire\Component;
use App\Models\Category;
use App\Models\User;
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

        $query = Category::with('creator')->orderBy('id', 'desc');

        if (!$user->hasRole('super admin')) {
            $query->where('created_by', $user->id);
        }

        $this->categories = $query->get();
    }

    public function openModal($id = null)
    {
        if ($id) {
            $category = Category::findOrFail($id);

            $this->authorize('update', $category);

            $this->categoryId = $category->id;
            $this->name = $category->name;
        } else {
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

        if ($this->categoryId) {

            $category = Category::findOrFail($this->categoryId);

            $this->authorize('update', $category);

            $category->update([
                'name' => $this->name,
                'slug' => Str::slug($this->name),
            ]);

        } else {

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

        $this->authorize('delete', $category);

        $category->delete();

        $this->loadCategories();
    }
};
?>

<div class="space-y-6 p-6 bg-[#F5F1ED] text-[#3B2F2A]">

    <h1 class="text-2xl font-bold mb-4 text-[#8B5E3C]">
        Categories
    </h1>

    <button wire:click="openModal"
        class="mb-4 px-4 py-2 bg-[#38BDF8] text-white rounded-lg hover:opacity-90 transition">
        Add New Category
    </button>

    <div class="bg-white border border-[#8B5E3C]/20 shadow rounded-xl">

        <div class="flex font-bold border-b border-[#8B5E3C]/20 p-3 text-[#8B5E3C]">
            <div class="w-1/6">ID</div>
            <div class="w-2/6">Name</div>

            @role('super admin')
                <div class="w-2/6">Created By</div>
            @endrole

            <div class="w-2/6">Actions</div>
        </div>

        @foreach ($categories as $category)
            <div class="flex border-b border-[#8B5E3C]/10 p-3 items-center">

                <div class="w-1/6">{{ $category->id }}</div>

                <div class="w-2/6 text-[#3B2F2A]">
                    {{ $category->name }}
                </div>

                @role('super admin')
                    <div class="w-2/6 text-[#3B2F2A]/70">
                        {{ $category->creator?->name ?? 'N/A' }}
                    </div>
                @endrole

                <div class="w-2/6 flex gap-2">

                    @can('update', $category)
                        <button wire:click="openModal({{ $category->id }})"
                            class="px-3 py-1 bg-[#38BDF8] text-white rounded hover:opacity-90">
                            Edit
                        </button>
                    @endcan

                    @can('delete', $category)
                        <button wire:click="deleteCategory({{ $category->id }})"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:opacity-90">
                            Delete
                        </button>
                    @endcan

                </div>

            </div>
        @endforeach

    </div>

    {{-- MODAL --}}
    @if ($modalOpen)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 z-50">

            <div class="bg-[#F5F1ED] border border-[#8B5E3C]/20 p-6 rounded-xl w-96">

                <h2 class="text-xl font-bold mb-4 text-[#8B5E3C]">
                    {{ $categoryId ? 'Edit Category' : 'Add Category' }}
                </h2>

                <input type="text"
                    wire:model="name"
                    class="border border-[#8B5E3C]/20 p-2 w-full mb-4 rounded bg-white text-[#3B2F2A]"
                    placeholder="Category Name">

                <div class="flex justify-end gap-2">

                    <button wire:click="closeModal"
                        class="px-4 py-2 bg-[#8B5E3C] text-white rounded hover:opacity-90">
                        Cancel
                    </button>

                    <button wire:click="saveCategory"
                        class="px-4 py-2 bg-[#38BDF8] text-white rounded hover:opacity-90">
                        {{ $categoryId ? 'Update' : 'Add' }}
                    </button>

                </div>

            </div>

        </div>
    @endif

</div>