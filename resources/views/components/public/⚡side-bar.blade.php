<?php

use Livewire\Component;
use App\Models\Category;

new class extends Component
{
    public $categories = [];

    public function mount()
    {
        $this->categories = Category::orderBy('name')->get();
    }
};
?>

<aside
    class="
        w-64
        flex-col
        h-screen sticky top-0
        bg-[#8B5E3C]
        text-white
    "
>

    {{-- ================= NAV ================= --}}
    <nav class="p-4 space-y-2 overflow-y-auto flex-1">

        @forelse($categories as $category)
            <a
                href="{{ route('categories.show', $category->slug) }}"
                class="
                    block px-4 py-3 rounded-lg
                    text-white/90
                    hover:bg-[#D4A373]/30
                    hover:text-white
                    transition
                "
            >
                {{ $category->name }}
            </a>
        @empty
            <p class="text-white/70 text-sm">
                No categories found
            </p>
        @endforelse

    </nav>

</aside>