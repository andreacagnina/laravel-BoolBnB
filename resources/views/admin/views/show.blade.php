@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Dettagli della proprietà -->
        <div class="col-md-6 d-flex flex-column justify-content-center">
            <div class="alert alert-warning text-center my-3">
                <h5 class="mb-1">Sponsorships</h5>
                <p class="display-6">{{ $property->sponsors->count() }}</p>
            </div>
            <div class="alert alert-primary text-center my-3">
                <h5 class="mb-1">Total Sponsorship Cost</h5>
                <p class="display-6">
                    {{ number_format($property->sponsors->sum('price'), 2, ',', '') }} &euro;
                </p>
            </div>
            <div class="alert alert-info text-center my-3">
                <h5 class="mb-1">Views</h5>
                <p class="display-6">{{ $property->views_count }}</p>
            </div>
            <div class="alert alert-danger text-center my-3">
                <h5 class="mb-1">Favorites</h5>
                <p class="display-6">{{ $property->favorites_count }}</p>
            </div>
            <div class="alert alert-success text-center my-3">
                <h5 class="mb-1">Messages Received</h5>
                <p class="display-6">{{ $property->messages()->withTrashed()->count() }}</p>
            </div>
        </div>

        <!-- Card della proprietà -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <h1>{{ $property->title }}</h1>
                    <p class="lead">{{ $property->description }}</p>
                </div>
                <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                    class="card-img-top img-fluid" alt="{{ $property->title }}">
                <div class="card-body d-flex flex-column justify-content-center align-items-center fs-5">
                    <p class="card-text"><strong>Price:</strong>
                        {{ number_format($property->price, 2, ',', '') }}&euro;
                    </p>
                    <p class="card-text"><strong>Type:</strong> {{ $property->type }}</p>
                    <p class="card-text"><strong>Location:</strong> {{ $property->address }}</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.properties.show', $property) }}" class="btn btn-primary">
                        View Full Details
                    </a>
                    <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Back to list</a>
                </div>
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

    <!-- Grafico Combinato -->
    <div class="row mt-5">
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

    // Calcola i limiti per le scale
    const calculateMaxScale = (data) => {
        const max = Math.max(...data);
        return max + Math.ceil(max * 0.1); // Aggiungi un margine del 10% al valore massimo
    };

    const createBarChart = (ctx, label, data, backgroundColor, borderColor) => {
        const maxScale = calculateMaxScale(data);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: backgroundColor,
                    borderColor: borderColor,
                    borderWidth: 1,
                }]
            },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        beginAtZero: true,
                        max: maxScale,
                        ticks: {
                            stepSize: Math.ceil(maxScale / 10) // Imposta step proporzionali
                        }
                    } 
                } 
            }
        });
    };

    const createCombinedChart = (ctx, barLabel, lineLabel, barData, lineData) => {
        const maxScale = Math.max(calculateMaxScale(barData), calculateMaxScale(lineData));
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        type: 'bar',
                        label: barLabel,
                        data: barData,
                        backgroundColor: 'rgb(255, 243, 205,0.5)',
                        borderColor: 'rgb(255, 243, 205,0.5)',
                    },
                    {
                        type: 'line',
                        label: lineLabel,
                        data: lineData,
                        borderColor: 'rgb(207, 244, 252, 1)',
                        backgroundColor: 'rgb(207, 244, 252, 1)',
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
                        beginAtZero: true,
                        max: maxScale,
                        ticks: {
                            stepSize: Math.ceil(maxScale / 10)
                        }
                    }
                }
            }
        });
    };

    // Creazione grafici
    document.addEventListener('DOMContentLoaded', () => {
        createBarChart(document.getElementById('viewsChart').getContext('2d'), 'Views', Object.values(monthlyData.views), 'rgb(207, 244, 252,1)', 'rgb(207, 244, 252, 1)');
        createBarChart(document.getElementById('messagesChart').getContext('2d'), 'Messages', Object.values(monthlyData.messages), 'rgb(209, 231, 221, 1)', 'rgb(209, 231, 221, 1)');
        createBarChart(document.getElementById('favoritesChart').getContext('2d'), 'Favorites', Object.values(monthlyData.favorites), 'rgb(248, 215, 218,1)', 'rgb(248, 215, 218,1)');
        createBarChart(document.getElementById('sponsorsChart').getContext('2d'), 'Sponsorships', Object.values(monthlyData.sponsors), 'rgb(255, 243, 205,1)', 'rgb(255, 243, 205,1)');
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
    /* Altezze minime per grafici */
    canvas {
        min-height: 250px; /* Per dispositivi mobili */
    }

    @media (min-width: 768px) {
        canvas {
            min-height: 300px; /* Per desktop */
        }
    }
</style>
@endsection
