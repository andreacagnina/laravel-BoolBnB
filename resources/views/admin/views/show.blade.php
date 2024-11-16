@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 d-flex flex-column justify-content-center">

                <div class="alert alert-warning text-center my-3">
                    <h5 class="mb-1">Sponsorships</h5>
                    <p class="display-6">{{ $property->sponsors->count() }}</p>
                </div>

                <div class="alert alert-primary text-center my-3">
                    <h5 class="mb-1">Total Sponsorship Cost</h5>
                    <p class="display-6">
                        {{ number_format($property->sponsors->sum('price'), 2, ',', '') }}
                        &euro;
                    </p>
                </div>

                <div class="alert alert-info text-center my-3">
                    <h5 class="mb-1">Views</h5>
                    <p class="display-6">{{ $property->views_count }}</p>
                </div>

                <div class="alert alert-danger text-center my-3">
                    <h5 class="mb-1">Favorites</h5>
                    <p class="display-6">{{ $property->favorites_count }}</p> <!-- Currently showing a fixed number of 0 -->
                </div>

                <div class="alert alert-success text-center my-3">
                    <h5 class="mb-1">Messages Received</h5>
                    <p class="display-6">{{ $property->messages()->withTrashed()->count() }}</p>
                </div>
            </div>

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
                        <a href="{{ route('admin.properties.show', $property) }}" class="btn btn-outline-primary">
                            View Full Details
                        </a>
                        <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Back to list</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
