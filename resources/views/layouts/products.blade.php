<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    {{-- VERY IMPORTANT --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Products | Home Pearls' }}</title>

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
    <meta name="twitter:title" content="|Products | Home Pearls">
    <meta name="twitter:description" content="Beautiful furniture and home essentials at Home Pearls.">
    <meta name="twitter:image" content="{{ asset('images/homepearls_logo.webp') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen flex flex-col overflow-x-hidden">

    {{-- NAVBAR --}}
    <livewire:nav-bar.nav-bar />

    {{-- CONTENT AREA --}}
    <div class="flex flex-1">

        {{-- SIDEBAR (desktop) --}}
        <aside class="hidden lg:block w-64 bg-white border-r">
            <livewire:public.side-bar />
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col overflow-x-hidden items-center">

            <main class="flex-1 p-6 max-w-7xl mx-auto w-full overflow-y-auto overflow-x-hidden items-center">
                {{ $slot }}
            </main>

        </div>

    </div>
    {{-- FOOTER --}}
            <footer>
                @include('partials.footer')
            </footer>

             {{-- FLOATING WHATSAPP BUTTON --}}
       {{-- FLOATING WHATSAPP BUTTON --}}
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