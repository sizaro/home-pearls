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
    <livewire:navbar.nav-bar />

    {{-- CONTENT AREA --}}
    <div class="flex flex-1">

        {{-- LEFT SIDE --}}
        <div class="flex-1 flex flex-col">

            {{-- HERO (optional) --}}
            @if(!isset($hideHero) || !$hideHero)
                <section class="w-full">
                    <x-hero.slider />
                </section>
            @endif

            {{-- MAIN --}}
            <main class="flex-1 p-6 max-w-7xl mx-auto w-full">
                {{ $slot }}
            </main>

            {{-- FOOTER --}}
            <x-footer.main-footer />

        </div>

        {{-- RIGHT SIDEBAR --}}
        <aside class="hidden lg:block w-72 bg-white border-l">
            <livewire:public.sidebar />
        </aside>

    </div>

    @livewireScripts
</body>
</html>