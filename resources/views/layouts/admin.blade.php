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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let pieChart = null;
let barChart = null;

function renderCharts(products = {}, variants = {}) {

    const pieCanvas = document.getElementById('productsPerCategory');
    const barCanvas = document.getElementById('variantsPerProduct');

    if (!pieCanvas || !barCanvas) {
        console.warn("Canvas not ready");
        return;
    }

    // ===== PIE CHART =====
    const pieLabels = Object.keys(products);
    const pieValues = Object.values(products);

    if (pieLabels.length > 0) {

        if (pieChart) pieChart.destroy();

        pieChart = new Chart(pieCanvas, {
            type: 'pie',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieValues,
                    backgroundColor: [
                        '#38BDF8', '#8B5E3C', '#3B2F2A',
                        '#E7DED5', '#A78BFA', '#34D399'
                    ]
                }]
            }
        });

    } else {
        console.warn("Pie chart has no data");
    }

    // ===== BAR CHART (WITH REAL COLORS) =====
    const barLabels = Object.keys(variants);
    const barValues = Object.values(variants);

    const palette = [
        '#38BDF8', '#8B5E3C', '#3B2F2A',
        '#E7DED5', '#A78BFA', '#34D399',
        '#F87171', '#FBBF24'
    ];

    const barColors = barLabels.map((_, i) => palette[i % palette.length]);

    if (barChart) barChart.destroy();

    barChart = new Chart(barCanvas, {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Variants',
                data: barValues,
                backgroundColor: barColors
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}


// ✅ VERY IMPORTANT — wait for Livewire + DOM
document.addEventListener('livewire:initialized', () => {

  Livewire.on('chartsUpdated', (data) => {

    console.log("Raw:", data);

    // FIX: extract real data from array
    const payload = Array.isArray(data) ? data[0] : data;

    console.log("Fixed:", payload);

    setTimeout(() => {
        renderCharts(
            payload.productsPerCategory || {},
            payload.variantsPerProduct || {}
        );
    }, 100);
});

});
</script>

</body>
</html>