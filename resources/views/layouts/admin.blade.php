<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
        <div class="flex-2 flex flex-col ml-4">

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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let pieChart = null;
let barChart = null;

document.addEventListener('livewire:init', () => {

    function renderCharts(productsPerCategory = {}, variantsPerProduct = {}) {

        const pieCanvas = document.getElementById('productsPerCategory');
        const barCanvas = document.getElementById('variantsPerProduct');

        if (!pieCanvas || !barCanvas) {
            console.log("Canvas not ready");
            return;
        }

        // PIE
        if (pieChart) pieChart.destroy();

        pieChart = new Chart(pieCanvas, {
            type: 'pie',
            data: {
                labels: Object.keys(productsPerCategory || {}),
                datasets: [{
                    data: Object.values(productsPerCategory || {}),
                    backgroundColor: ['#38BDF8', '#8B5E3C', '#3B2F2A', '#E7DED5'],
                }]
            }
        });

        // BAR
        if (barChart) barChart.destroy();

        barChart = new Chart(barCanvas, {
            type: 'bar',
            data: {
                labels: Object.keys(variantsPerProduct || {}),
                datasets: [{
                    label: 'Variants',
                    data: Object.values(variantsPerProduct || {}),
                    backgroundColor: '#38BDF8'
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // 🔥 LISTEN FOR LIVEWIRE EVENT
    Livewire.on('chartsUpdated', (data) => {
        renderCharts(
            data.productsPerCategory,
            data.variantsPerProduct
        );
    });

});
</script>
</body>
</html>