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

    <!-- Grafico a ciambella -->
    <div class="row mt-5">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <h5>Interactions Distribution</h5>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <canvas id="interactionDistributionChart" width="400" height="400"></canvas>
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

    // Funzioni per grafici
    const createBarChart = (ctx, label, data, backgroundColor, borderColor) => {
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
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    };

    const createDoughnutChart = (ctx, labels, data, colors) => {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                }]
            },
            options: { responsive: true }
        });
    };

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
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    };

    // Creazione grafico combinato (sponsorships vs views mensili)
    createCombinedChart(
        document.getElementById('sponsorsVsViewsChart').getContext('2d'),
        'Sponsorships',
        'Views',
        Object.values(monthlyData.sponsors),
        Object.values(monthlyData.views) // Usando le views mensili, non cumulative
    );

    document.addEventListener('DOMContentLoaded', () => {
        createBarChart(document.getElementById('viewsChart').getContext('2d'), 'Views', Object.values(monthlyData.views), 'rgb(207, 244, 252,1)', 'rgb(207, 244, 252, 1)');
        createBarChart(document.getElementById('messagesChart').getContext('2d'), 'Messages', Object.values(monthlyData.messages), 'rgb(209, 231, 221, 1)', 'rgb(209, 231, 221, 1)');
        createBarChart(document.getElementById('favoritesChart').getContext('2d'), 'Favorites', Object.values(monthlyData.favorites), 'rgb(248, 215, 218,1)', 'rgb(248, 215, 218,1)');
        createBarChart(document.getElementById('sponsorsChart').getContext('2d'), 'Sponsorships', Object.values(monthlyData.sponsors), 'rgb(255, 243, 205,1)', 'rgb(255, 243, 205,1)');

        const totalInteractions = [
            Object.values(monthlyData.views).reduce((a, b) => a + b, 0),
            Object.values(monthlyData.messages).reduce((a, b) => a + b, 0),
            Object.values(monthlyData.favorites).reduce((a, b) => a + b, 0),
            Object.values(monthlyData.sponsors).reduce((a, b) => a + b, 0)
        ];

        createDoughnutChart(
            document.getElementById('interactionDistributionChart').getContext('2d'),
            ['Views', 'Messages', 'Favorites', 'Sponsorships'],
            totalInteractions,
            ['rgb(207, 244, 252, 1)', 'rgb(209, 231, 221, 1)', 'rgb(248, 215, 218,1)', 'rgb(255, 243, 205,1)']
        );

        const sponsorsData = Object.values(monthlyData.sponsors);
        const cumulativeViews = Object.values(monthlyData.views).reduce((acc, val) => {
            acc.push((acc.slice(-1)[0] || 0) + val);
            return acc;
        }, []);

        createCombinedChart(
            document.getElementById('sponsorsImpactChart').getContext('2d'),
            'Monthly Sponsorships',
            'Cumulative Views',
            sponsorsData,
            cumulativeViews
        );
    });
</script>
@endsection
