@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row mb-4 text-center">
            <div class="col-12">
                <h1 class="display-4">Welcome to BoolBnB</h1>
                <p class="lead text-muted">Find the perfect property, whether it’s sponsored or not!</p>

                <!-- Search Bar with Filter Button -->
                <div class="col-lg-8 offset-lg-2 mt-4 position-relative">
                    <div class="input-group mb-3 shadow-sm rounded-pill">
                        <input type="text" id="citySearch" class="form-control form-control-lg border-0"
                            placeholder="Search by city or address" aria-label="citySearch" autocomplete="off">
                        <button id="searchButton" class="btn btn-primary">Search</button>
                        <!-- Filter Button -->
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#filterModal">
                            <i class="bi bi-sliders"></i> Filtri
                        </button>
                    </div>
                    <!-- Suggestions displayed below the search bar -->
                    <div id="suggestions" class="list-group position-absolute w-100 mt-1 shadow-sm" style="z-index: 1000;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Filters -->
        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Usa modal-lg per una larghezza maggiore -->
                <div class="modal-content" style="max-height: 80vh; overflow-y: auto;">
                    <!-- Limita l'altezza e rimuovi overflow -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filtri di Ricerca</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Filters inside the modal -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="rooms" class="form-label fw-bold">Min Rooms</label>
                                <input type="number" id="rooms" class="form-control" placeholder="e.g. 2"
                                    min="1" value="1">
                            </div>
                            <div class="col-md-4">
                                <label for="beds" class="form-label fw-bold">Min Beds</label>
                                <input type="number" id="beds" class="form-control" placeholder="e.g. 1"
                                    min="1" value="1">
                            </div>
                            <div class="col-md-4">
                                <label for="radius" class="form-label fw-bold">Radius (km)</label>
                                <input type="number" id="radius" class="form-control" placeholder="e.g. 20"
                                    min="1" value="20">
                            </div>
                        </div>

                        <!-- Services Filters -->
                        <div class="mt-3">
                            <label class="form-label fw-bold">Services:</label>
                            <div class="row">
                                @foreach ($services as $service)
                                    <div class="col-6 col-md-4 d-flex align-items-center">
                                        <input class="form-check-input me-2" type="checkbox" value="{{ $service->id }}"
                                            id="service-{{ $service->id }}" name="services[]">
                                        <label class="form-check-label" for="service-{{ $service->id }}">
                                            {{ $service->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="resetFiltersButton" class="btn btn-outline-secondary">Reset
                            Filtri</button>
                        <button type="button" id="applyFiltersButton" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container for displaying search results dynamically -->
        <div class="row" id="resultsContainer">
            @forelse ($properties as $property)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm {{ $property->sponsored ? 'border-success' : '' }}">
                        @if ($property->sponsored)
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">Sponsored</span>
                        @endif
                        <div class="overflow-hidden" style="height: 200px;">
                            <img src="{{ $property->cover_image_url }}" class="card-img-top w-100 h-100"
                                style="object-fit: cover;" alt="{{ $property->title }}">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $property->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($property->description, 60) }}</p>
                            <p class="card-text"><strong>Price:</strong>
                                {{ number_format($property->price, 2, ',', '') }}&euro;
                            </p>
                            <p class="card-text"><strong>Location:</strong> {{ $property->address }}</p>
                            @if (isset($property->distance))
                                <p class="card-text"><strong>Distance:</strong> {{ $property->distance }} km</p>
                            @endif
                            <a href="{{ route('properties.show', ['slug' => $property->slug]) }}"
                                class="mt-auto btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>
            @empty
                <p>No properties found within the specified criteria.</p>
            @endforelse
        </div>
    </div>
@endsection
