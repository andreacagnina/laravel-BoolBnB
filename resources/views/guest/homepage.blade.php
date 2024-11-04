@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row mb-4 text-center">
            <div class="col-12">
                <h1 class="display-4">Benvenuto su BoolBnB</h1>
                <p class="lead text-muted">Trova la proprietà perfetta, che sia sponsorizzata o meno!</p>
            </div>
        </div>

        <div class="row">
            @foreach ($properties as $property)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm {{ $property->sponsored ? 'border-success' : '' }}">
                        @if ($property->sponsored)
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">Sponsorizzata</span>
                        @endif
                        <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                            class="card-img-top img-fluid rounded-top" alt="{{ $property->title }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $property->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($property->description, 60) }}</p>
                            <p class="card-text"><strong>Prezzo:</strong> €{{ number_format($property->price, 2) }}</p>
                            <p class="card-text"><strong>Località:</strong> {{ $property->address }}</p>
                            <a href="{{ route('properties.show', ['slug' => $property->slug]) }}"
                                class="mt-auto btn btn-outline-primary">
                                Visualizza Dettagli
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
