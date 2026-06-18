<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    {{-- VERY IMPORTANT --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Home Pearls' }}</title>

    {{-- ================= FAVICON (browser tab icon) ================= --}}
    <link rel="icon" type="image/webp" href="{{ asset('images/homepearls_logo.webp') }}">
    <link rel="shortcut icon" href="{{ asset('images/homepearls_logo.webp') }}">

    {{-- ================= OPEN GRAPH (Facebook, WhatsApp, LinkedIn) ================= --}}
    <meta property="og:title" content="Home Pearls">
    <meta property="og:description" content="Beautiful furniture and home essentials at Home Pearls.">
    <meta property="og:image" content="{{ asset('images/homepearls_logo.webp') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- ================= TWITTER CARD ================= --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Home Pearls">
    <meta name="twitter:description" content="Beautiful furniture and home essentials at Home Pearls.">
    <meta name="twitter:image" content="{{ asset('images/homepearls_logo.webp') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen flex flex-col overflow-x-hidden">

    {{-- NAVBAR --}}
    <livewire:nav-bar.nav-bar />

    {{-- MAIN WRAPPER --}}
    <div class="flex flex-1 w-full">

        {{-- SIDEBAR --}}
        <aside class="hidden md:block w-64 shrink-0">
            <livewire:public.side-bar />
        </aside>

        {{-- CONTENT AREA --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- HERO --}}
            @if(!isset($hideHero) || !$hideHero)
                <section class="w-full px-4 pt-4">
                    <div class="max-w-7xl mx-auto w-full">
                        <livewire:public.hero-slider />
                    </div>
                </section>
            @endif

            {{-- MAIN CONTENT --}}
            <main class="flex-1 w-full px-4 md:px-6 py-6">
                <div class="max-w-7xl mx-auto w-full">
                    {{ $slot }}
                </div>
            </main>

        </div>

    </div>

    {{-- FOOTER --}}
    <footer class="w-full">
        @include('partials.footer')
    </footer>

    {{-- WHATSAPP BUTTON --}}
    <a href="https://wa.me/256701234567"
       target="_blank"
       class="fixed bottom-8 right-5 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg z-50 transition transform hover:scale-110 flex items-center justify-center">

        <img src="{{ asset('images/whatsapp_icon.png') }}"
             alt="WhatsApp"
             class="w-10 h-10 rounded-full">
    </a>

    @livewireScripts
</body>
</html>