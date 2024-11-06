@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row mb-4 text-center">
            <div class="col-12">
                <h1 class="display-4">Welcome to BoolBnB</h1>
                <p class="lead text-muted">Find the perfect property, whether it’s sponsored or not!</p>
                <div class="col-6 offset-3">
                    <div class="input-group position-relative">
                        <input type="text" id="citySearch" class="form-control rounded" placeholder="🔍 Searchbar"
                            aria-label="citySearch" aria-describedby="addon-wrapping">
                        <div id="suggestions" class="suggestions-list-home border-0 rounded text-start"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @forelse ($properties as $property)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm {{ $property->sponsored ? 'border-success' : '' }}">
                        @if ($property->sponsored)
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">Sponsored</span>
                        @endif
                        <div class="overflow-hidden" style="height: 200px;">
                            <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                                class="card-img-top w-100 h-100" style="object-fit: cover;" alt="{{ $property->title }}">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $property->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($property->description, 60) }}</p>
                            <p class="card-text"><strong>Price:</strong>
                                {{ number_format($property->price, 2, ',', '') }}&euro;
                            </p>
                            <p class="card-text"><strong>Location:</strong> {{ $property->address }}</p>
                            <a href="{{ route('properties.show', ['slug' => $property->slug]) }}"
                                class="mt-auto btn btn-outline-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p>Nessuna proprietà trovata entro 20 km dalla posizione cercata.</p>
            @endforelse
        </div>
    </div>
@endsection
