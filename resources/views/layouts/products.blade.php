<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Home Pearls' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

    {{-- NAVBAR --}}
    <livewire:nav-bar.nav-bar />

    {{-- CONTENT AREA --}}
    <div class="flex flex-1">

        {{-- SIDEBAR (desktop) --}}
        <aside class="hidden lg:block w-64 bg-white border-r">
            <x-public.side-bar />
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col">

            <main class="flex-1 p-6 max-w-7xl mx-auto w-full">
                {{ $slot }}
            </main>

        </div>

    </div>
    {{-- FOOTER --}}
            <footer>
                @include('partials.footer')
            </footer>
    @livewireScripts
</body>
</html>