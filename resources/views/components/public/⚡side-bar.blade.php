<?php

use Livewire\Component;

new class extends Component
{
    // Later this will come from database
    public array $categories = [
        ['name' => 'Beds', 'slug' => 'beds'],
        ['name' => 'Chairs', 'slug' => 'chairs'],
        ['name' => 'Sofas', 'slug' => 'sofas'],
        ['name' => 'Dining Tables', 'slug' => 'dining-tables'],
        ['name' => 'Office Desks', 'slug' => 'office-desks'],
        ['name' => 'Wardrobes', 'slug' => 'wardrobes'],
        ['name' => 'Doors', 'slug' => 'doors'],
        ['name' => 'Metal Gates', 'slug' => 'metal-gates'],
        ['name' => 'Railings', 'slug' => 'railings'],
        ['name' => 'Kitchen Cabinets', 'slug' => 'kitchen-cabinets'],
        ['name' => 'Custom Wood & Metal', 'slug' => 'custom-wood-metal'],
    ];
};
?>

<aside class="w-64 bg-white border-r h-screen sticky top-0 flex flex-col">

    <div class="p-6 font-bold text-xl border-b">
        Product Categories
    </div>

    <nav class="p-4 space-y-1 text-gray-700 overflow-y-auto flex-1">

        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category['slug']) }}"
               class="block px-4 py-2 rounded hover:bg-gray-100">
                {{ $category['name'] }}
            </a>
        @endforeach

    </nav>

</aside>