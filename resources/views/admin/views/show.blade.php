@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-md-6 d-flex flex-column justify-content-center">
                <h1 class="display-4">{{ $property->title }}</h1>
                <p class="lead text-muted">{{ $property->description }}</p>

                <div class="alert alert-info text-center my-3">
                    <h5 class="mb-1">Visualizzazioni</h5>
                    <p class="display-6">{{ $property->views_count }}</p>
                </div>

                <div class="alert alert-danger text-center my-3">
                    <h5 class="mb-1">Preferiti</h5>
                    <p class="display-6">0</p> <!-- Qui mettiamo il numero fisso di 0 per ora -->
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                        class="card-img-top img-fluid rounded" alt="{{ $property->title }}">
                    <div class="card-body">
                        <p class="card-text"><strong>Prezzo:</strong> €{{ number_format($property->price, 2) }}</p>
                        <p class="card-text"><strong>Tipo:</strong> {{ $property->type }}</p>
                        <p class="card-text"><strong>Località:</strong> {{ $property->address }}</p>
                        <a href="{{ route('admin.properties.show', $property) }}" class="btn btn-outline-primary">
                            Visualizza Dettagli Completi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
