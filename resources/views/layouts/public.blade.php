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
    <div class="flex flex-1 mt-1">

        {{-- RIGHT SIDEBAR --}}
             <livewire:public.side-bar />

        {{-- LEFT SIDE --}}
        <div class="flex-2 flex flex-col ml-4 mt-">

            {{-- HERO (optional) --}}
            @if(!isset($hideHero) || !$hideHero)
                <section class="w-full">
                    <livewire:public.hero-slider />
                </section>
            @endif

            {{-- MAIN --}}
            <main class="flex-1 p-6 max-w-7xl mx-auto w-full overflow-y-auto">
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