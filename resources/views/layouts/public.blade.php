<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    {{-- ✅ VERY IMPORTANT --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Home Pearls' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen flex flex-col overflow-x-hidden items-center">

    {{-- NAVBAR --}}
    <livewire:nav-bar.nav-bar />

    {{-- CONTENT AREA --}}
    <div class="flex flex-1">

        {{-- SIDEBAR (desktop only) --}}
        <div class="hidden md:block w-64">
            <livewire:public.side-bar />
        </div>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col ">

            {{-- HERO --}}
            @if(!isset($hideHero) || !$hideHero)
                <section class="md:p-4 max-w-7xl mx-auto md:w-full w-screen p-4">
                    <livewire:public.hero-slider />
                </section>
            @endif

            {{-- MAIN --}}
            <main class="flex-1 p-4 md:p-6 max-w-7xl mx-auto w-full">
                {{ $slot }}
            </main>

        </div>

    </div>

    {{-- FOOTER --}}
    <footer>
        @include('partials.footer')
    </footer>

    {{-- FLOATING WHATSAPP BUTTON --}}
    <a href="https://wa.me/256701234567"
       target="_blank"
       class="fixed bottom-5 right-5 bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg z-50 transition transform hover:scale-110">

        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 fill-white" viewBox="0 0 24 24">
            <path d="M20 3.5A11.5 11.5 0 0 0 2.6 18.7L2 22l3.4-.9A11.5 11.5 0 1 0 20 3.5zm-8.5 17a9 9 0 0 1-4.6-1.3l-.3-.2-2.1.6.6-2-.2-.3A9 9 0 1 1 11.5 20.5z"/>
        </svg>

    </a>

    @livewireScripts
</body>
</html>