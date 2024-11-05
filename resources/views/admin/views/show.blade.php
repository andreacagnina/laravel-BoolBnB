@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-6 d-flex flex-column justify-content-center">
                <h1 class="display-4">{{ $property->title }}</h1>
                <p class="lead text-muted">{{ $property->description }}</p>

                <div class="alert alert-info text-center my-3">
                    <h5 class="mb-1">Views</h5>
                    <p class="display-6">{{ $property->views_count }}</p>
                </div>

                <div class="alert alert-danger text-center my-3">
                    <h5 class="mb-1">Favorites</h5>
                    <p class="display-6">0</p> <!-- Currently showing a fixed number of 0 -->
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                        class="card-img-top img-fluid rounded" alt="{{ $property->title }}">
                    <div class="card-body">
                        <p class="card-text"><strong>Price:</strong> €{{ number_format($property->price, 2) }}</p>
                        <p class="card-text"><strong>Type:</strong> {{ $property->type }}</p>
                        <p class="card-text"><strong>Location:</strong> {{ $property->address }}</p>
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
