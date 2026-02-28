<aside class="w-64 bg-white border-r h-screen sticky top-0 flex flex-col">

    <div class="p-6 font-bold text-xl border-b">
        Product Categories
    </div>

    <nav class="p-4 space-y-1 text-gray-700 overflow-y-auto flex-1">

        <a href="{{ route('products') }}?category=beds"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Beds
        </a>

        <a href="{{ route('products') }}?category=chairs"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Chairs
        </a>

        <a href="{{ route('products') }}?category=sofas"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Sofas
        </a>

        <a href="{{ route('products') }}?category=dining-tables"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Dining Tables
        </a>

        <a href="{{ route('products') }}?category=office-desks"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Office Desks
        </a>

        <a href="{{ route('products') }}?category=wardrobes"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Wardrobes
        </a>

        <a href="{{ route('products') }}?category=doors"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Doors
        </a>

        <a href="{{ route('products') }}?category=metal-gates"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Metal Gates
        </a>

        <a href="{{ route('products') }}?category=railings"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Railings
        </a>

        <a href="{{ route('products') }}?category=kitchen-cabinets"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Kitchen Cabinets
        </a>

        <a href="{{ route('products') }}?category=custom-wood-metal"
           class="block px-4 py-2 rounded hover:bg-gray-100">
            Custom Wood & Metal
        </a>

    </nav>

</aside>