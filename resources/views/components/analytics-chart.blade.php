<hr class="my-6">

        <h3 class="text-lg font-semibold mb-4">Monthly Traffic Overview</h3>

        <canvas id="visitorsChart" height="100"></canvas>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        const ctx = document.getElementById('visitorsChart').getContext('2d');

        new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($dates),
            datasets: [
            {
                label: 'Unique Visitors',
                data: @json($visitors),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                pointRadius: 3
            },
            {
                label: 'Pageviews',
                data: @json($pageviews),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                pointRadius: 3
            }
            ]
        },
        options: {
            responsive: true,
            plugins: {
            legend: { position: 'top' },
            tooltip: { mode: 'index', intersect: false }
            },
            interaction: { mode: 'nearest', intersect: false },
            scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
            }
        }
        });
        </script>