@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <h2 class="mb-4 text-center">Summary Statistics</h2>

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

        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-dark text-center">
                    <h5 class="mb-1">Average Property Price</h5>
                    <p class="display-6">{{ number_format($stats['average_price'], 2, ',', '') }}&euro;</p>
                </div>
            </div>
        </div>
    </div>
@endsection
