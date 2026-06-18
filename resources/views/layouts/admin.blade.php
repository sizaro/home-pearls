<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    {{-- VERY IMPORTANT --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Admin | Home Pearls' }}</title>

    {{-- ================= FAVICON (browser tab icon) ================= --}}
    <link rel="icon" type="image/webp" href="{{ asset('images/homepearls_logo.webp') }}">
    <link rel="shortcut icon" href="{{ asset('images/homepearls_logo.webp') }}">

    {{-- ================= OPEN GRAPH (Facebook, WhatsApp, LinkedIn) ================= --}}
    <meta property="og:title" content="Admin | Home Pearls">
    <meta property="og:description" content="Beautiful furniture and home essentials at Home Pearls.">
    <meta property="og:image" content="{{ asset('images/homepearls_logo.webp') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    {{-- ================= TWITTER CARD ================= --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Admin | Home Pearls">
    <meta name="twitter:description" content="Beautiful furniture and home essentials at Home Pearls.">
    <meta name="twitter:image" content="{{ asset('images/homepearls_logo.webp') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-100 min-h-screen flex flex-col overflow-x-hidden">

    {{-- NAVBAR --}}
    <livewire:nav-bar.admin-nav-bar />

    {{-- CONTENT AREA --}}
    <div class="flex flex-1 mt-1">

     {{-- RIGHT SIDEBAR --}}
     <div class="hidden md:flex">
        <livewire:admin.side-bar />
     </div>
             
        {{-- LEFT SIDE --}}
        <div class="flex-2 flex flex-col ml-1 md:ml-4 overflow-x-hidden items-center">

            {{-- MAIN --}}
            <main class="flex-1 p-6 max-w-7xl mx-auto w-full overflow-y-auto overflow-x-hidden items-center">
                {{ $slot }}
            </main>

        </div>
        

    </div>
      {{-- FOOTER --}}
             <footer>
                @include('partials.footer')
            </footer>
     
    @livewireScripts

{{-- ✅ CHART SCRIPT --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let pieChart, barChart, visitsChart;

function renderCharts(payload) {

    const products = payload.productsPerCategory || {};
    const variants = payload.variantsPerProduct || {};
    const visits = payload.visitsDaily || {};

    // ================= PIE =================
    const pieCanvas = document.getElementById('productsPerCategory');
    if (pieCanvas) {

        if (pieChart) pieChart.destroy();

        pieChart = new Chart(pieCanvas, {
            type: 'pie',
            data: {
                labels: Object.keys(products),
                datasets: [{
                    data: Object.values(products),
                    backgroundColor: ['#38BDF8','#8B5E3C','#3B2F2A','#E7DED5','#34D399']
                }]
            }
        });
    }

    // ================= BAR =================
    const barCanvas = document.getElementById('variantsPerProduct');
    if (barCanvas) {

        if (barChart) barChart.destroy();

        barChart = new Chart(barCanvas, {
            type: 'bar',
            data: {
                labels: Object.keys(variants),
                datasets: [{
                    label: 'Variants',
                    data: Object.values(variants),
                    backgroundColor: '#38BDF8'
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    // ================= VISITS LINE =================
    const visitCanvas = document.getElementById('visitsDailyChart');
    if (visitCanvas) {

        if (visitsChart) visitsChart.destroy();

        visitsChart = new Chart(visitCanvas, {
            type: 'line',
            data: {
                labels: Object.keys(visits),
                datasets: [{
                    label: 'Daily Visits',
                    data: Object.values(visits),
                    borderWidth: 3,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }
}


// ================= LIVEWIRE HOOK =================
document.addEventListener('livewire:initialized', () => {

    Livewire.on('chartsUpdated', (data) => {

        const payload = Array.isArray(data) ? data[0] : data;

        setTimeout(() => {
            renderCharts(payload);
        }, 200);

    });

});
</script>

</body>
</html>