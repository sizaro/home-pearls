<?php

use Livewire\Component;
use App\Models\Category; // make sure this exists

new class extends Component
{
    public $categories = [];

    public function mount()
    {
        // Fetch all categories from the database, ordered by name
        $this->categories = Category::orderBy('name')->get();
    }
};
?>

<aside class="w-64 bg-white border-r h-screen sticky top-0 flex flex-col">

    <div class="p-6 font-bold text-xl border-b">
        Product Categories
    </div>

    <nav class="p-4 space-y-1 text-gray-700 overflow-y-auto flex-1">

        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}"
               class="block px-4 py-2 rounded hover:bg-gray-100">
                {{ $category->name }}
            </a>
        @endforeach

    </nav>

</aside>