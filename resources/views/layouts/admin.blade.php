<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Home Pearls' }}</title>

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
        <div class="flex-2 flex flex-col ml-1 md:ml-4">

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

{{-- ✅ CHART SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let pieChart = null;
let barChart = null;

document.addEventListener('livewire:init', () => {

    function renderCharts(products = {}, variants = {}) {

        const pieCanvas = document.getElementById('productsPerCategory');
        const barCanvas = document.getElementById('variantsPerProduct');

        if (!pieCanvas || !barCanvas) return;

        // COLORS
        const colors = [
            '#38BDF8', '#8B5E3C', '#3B2F2A', '#E7DED5',
            '#A78BFA', '#34D399', '#F87171', '#FBBF24'
        ];

        // PIE
        if (pieChart) pieChart.destroy();

        pieChart = new Chart(pieCanvas, {
            type: 'pie',
            data: {
                labels: Object.keys(products),
                datasets: [{
                    data: Object.values(products),
                    backgroundColor: colors
                }]
            }
        });

        // BAR
        if (barChart) barChart.destroy();

        barChart = new Chart(barCanvas, {
            type: 'bar',
            data: {
                labels: Object.keys(variants),
                datasets: [{
                    label: 'Variants',
                    data: Object.values(variants),
                    backgroundColor: colors.slice(0, Object.keys(variants).length)
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // LISTEN FROM LIVEWIRE
    Livewire.on('chartsUpdated', (data) => {
        console.log("Chart data:", data); // 👈 DEBUG IN CONSOLE
        renderCharts(
            data.productsPerCategory || {},
            data.variantsPerProduct || {}
        );
    });

});
</script>
</body>
</html>