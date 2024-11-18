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
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <h5>Monthly Views</h5>
                </div>
                <div class="card-body">
                    <canvas id="viewsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <h5>Monthly Messages</h5>
                </div>
                <div class="card-body">
                    <canvas id="messagesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <h5>Monthly Favorites</h5>
                </div>
                <div class="card-body">
                    <canvas id="favoritesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <h5>Monthly Sponsorships</h5>
                </div>
                <div class="card-body">
                    <canvas id="sponsorsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafico combinato -->
    <div class="row my-3">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <h5>Monthly Sponsorships and Views</h5>
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

    // Funzione per creare un grafico a barre
    const createBarChart = (ctx, label, data, backgroundColor) => {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: backgroundColor
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    };

    // Funzione per creare un grafico combinato
    const createCombinedChart = (ctx, barLabel, lineLabel, barData, lineData) => {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        type: 'bar',
                        label: barLabel,
                        data: barData,
                        backgroundColor: 'rgb(255, 243, 205,0.5)'
                    },
                    {
                        type: 'line',
                        label: lineLabel,
                        data: lineData,
                        borderColor: 'rgb(75, 192, 192, 1)',
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
                    y: { beginAtZero: true }
                }
            }
        });
    };

    // Creazione dei grafici
    document.addEventListener('DOMContentLoaded', () => {
        createBarChart(document.getElementById('viewsChart').getContext('2d'), 'Views', Object.values(monthlyData.views), 'rgb(207, 244, 252,1)');
        createBarChart(document.getElementById('messagesChart').getContext('2d'), 'Messages', Object.values(monthlyData.messages), 'rgb(209, 231, 221, 1)');
        createBarChart(document.getElementById('favoritesChart').getContext('2d'), 'Favorites', Object.values(monthlyData.favorites), 'rgb(248, 215, 218,1)');
        createBarChart(document.getElementById('sponsorsChart').getContext('2d'), 'Sponsorships', Object.values(monthlyData.sponsors), 'rgb(255, 243, 205,1)');
        createCombinedChart(
            document.getElementById('sponsorsVsViewsChart').getContext('2d'),
            'Sponsorships',
            'Views',
            Object.values(monthlyData.sponsors),
            Object.values(monthlyData.views)
        );
    });
</script>

<style>
    /* Stile responsivo per canvas */
    canvas {
        min-height: 200px; /* Mobile */
    }

    @media (min-width: 768px) {
        canvas {
            min-height: 300px; /* Desktop */
        }
    }
</style>
@endsection
