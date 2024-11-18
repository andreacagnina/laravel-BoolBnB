@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center mt-2 mb-5">Summary Statistics</h1>

    <!-- Statistiche sommarie -->
    <div class="row">
        <div class="col-md-4">
            <div class="alert alert-primary text-center">
                <h5 class="mb-1">Total Properties</h5>
                <p class="display-6">{{ $stats['total_properties'] }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-warning text-center">
                <h5 class="mb-1">Total Sponsorships</h5>
                <p class="display-6">{{ $stats['total_sponsorships'] }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-success text-center">
                <h5 class="mb-1">Total Sponsorship Cost</h5>
                <p class="display-6">{{ number_format($stats['total_sponsorship_cost'], 2, ',', '') }}&euro;</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="alert alert-info text-center">
                <h5 class="mb-1">Total Views</h5>
                <p class="display-6">{{ $stats['total_views'] }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-danger text-center">
                <h5 class="mb-1">Total Favorites</h5>
                <p class="display-6">{{ $stats['total_favorites'] }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="alert alert-secondary text-center">
                <h5 class="mb-1">Total Messages</h5>
                <p class="display-6">{{ $stats['total_messages'] }}</p>
            </div>
        </div>
    </div>

    <!-- Grafici -->
    <div class="row mt-5">
        <!-- Monthly Interactions e Interactions Distribution nella stessa riga -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header text-center">
                    <h5>Monthly Interactions (Aggregated)</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyBarChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header text-center">
                    <h5>Interactions Distribution</h5>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <canvas id="interactionDoughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Property Types Distribution e Sponsorships vs Views nella stessa riga -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header text-center">
                    <h5>Property Types Distribution</h5>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <canvas id="propertyTypePieChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header text-center">
                    <h5>Sponsorships vs Views</h5>
                </div>
                <div class="card-body">
                    <canvas id="sponsorsVsViewsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    const monthlyData = @json($monthlyData);
    const propertyTypes = @json($propertyTypes); // Aggiunto per il grafico a torta

    // Grafico a barre mensile
    new Chart(document.getElementById('monthlyBarChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                { label: 'Views', data: Object.values(monthlyData.views), backgroundColor: 'rgb(207, 244, 252,1)' },
                { label: 'Messages', data: Object.values(monthlyData.messages), backgroundColor: 'rgb(226, 227, 229,1)' },
                { label: 'Favorites', data: Object.values(monthlyData.favorites), backgroundColor: 'rgb(248, 215, 218,1)' },
                { label: 'Sponsorships', data: Object.values(monthlyData.sponsors), backgroundColor: 'rgb(255, 243, 205,1)' }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    // Grafico a ciambella
    const totalInteractions = [
        Object.values(monthlyData.views).reduce((a, b) => a + b, 0),
        Object.values(monthlyData.messages).reduce((a, b) => a + b, 0),
        Object.values(monthlyData.favorites).reduce((a, b) => a + b, 0),
        Object.values(monthlyData.sponsors).reduce((a, b) => a + b, 0)
    ];

    new Chart(document.getElementById('interactionDoughnutChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Views', 'Messages', 'Favorites', 'Sponsorships'],
            datasets: [{
                data: totalInteractions,
                backgroundColor: ['rgb(207, 244, 252,1)', 'rgb(226, 227, 229,1)', 'rgb(248, 215, 218,1)', 'rgb(255, 243, 205,1)']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Sponsorships vs Views (combinato)
    new Chart(document.getElementById('sponsorsVsViewsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                {
                    type: 'bar',
                    label: 'Sponsorships',
                    data: Object.values(monthlyData.sponsors),
                    backgroundColor: 'rgb(209, 231, 221,1)',
                    borderColor: 'rgb(55, 167, 90)',
                    borderWidth: 1
                },
                {
                    type: 'line',
                    label: 'Views',
                    data: Object.values(monthlyData.views),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgb(207, 244, 252,1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Grafico a torta per i tipi di proprietà
    new Chart(document.getElementById('propertyTypePieChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: Object.keys(propertyTypes),
            datasets: [{
                data: Object.values(propertyTypes),
                backgroundColor: ['rgba(54, 162, 235, 1)','rgb(234, 66, 54)', 'rgba(255, 206, 86, 1)', 'rgb(207, 244, 252,1)', 'rgb(226, 227, 229,1)','rgb(55, 167, 90)', 'rgb(255, 243, 205,1)']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endsection
