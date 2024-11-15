@extends('layouts.app')

@section('content')
    {{-- <div class="container my-4">
        <div class="row mb-4 text-center">
            <div class="col-12">
                <h1 class="display-4 fw-bold">Welcome to BoolBnB</h1>
                <p class="lead">Find the perfect property, whether it’s sponsored or not!</p>

                <!-- Search Bar with Filter Button -->
                <div class="col-lg-8 offset-lg-2 mt-4 position-relative">
                    <div class="input-group mb-3 shadow-sm rounded-pill">
                        <input type="text" id="citySearch" class="form-control form-control-lg border-active"
                            placeholder="Search by city or address" aria-label="citySearch" autocomplete="off">
                        <button id="searchButton" class="btn btn-primary">Search</button>
                        <button type="button" class="btn btn-custom" data-bs-toggle="modal"
                            data-bs-target="#filterModal">
                            <i class="bi bi-sliders"></i> Filters
                        </button>
                    </div>
                    <div id="suggestions" class="list-group position-absolute w-100 mt-1 shadow-sm suggestions-list-home"
                        style="z-index: 1000;">
                    </div>
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                </div>
            </div>
        </div>

        <!-- Filter Modal -->
        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="max-height: 80vh; overflow-y: auto;">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="filterModalLabel" style="color: #192033">Search Filters</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"  style="color: #192033">
                        <!-- Filters in Modal -->
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
                                            <i class="me-2{{ $service->icon }}"></i> {{ $service->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="resetFiltersButton" class="btn btn-outline-secondary">Reset
                            Filters</button>
                        <button type="button" id="applyFiltersButton" class="btn btn-custom" data-bs-dismiss="modal">Apply
                            Filters</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Container -->
        <div class="row" id="resultsContainer">
            @forelse ($properties as $property)
                <div class="col-md-3 mb-4">
                    <div class="card homepage-card h-100 {{ $property->sponsored }}">

                        @if ($property->sponsored)
                            <span class="badge bg-success position-absolute top-0 end-0 "
                                style="z-index: 10; font-size: 0.9rem; font-weight: bold; color: #192033;"><i class="fa-solid fa-crown"></i> Sponsored <i class="fa-solid fa-crown"></i></span>
                        @endif

                        <!-- Carousel for property images, without auto-slide -->
                        <div id="carousel-{{ $property->id }}" class="carousel slide" data-bs-ride="false">
                            <!-- Carousel indicators (dots) -->
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#carousel-{{ $property->id }}"
                                    data-bs-slide-to="0" class="active" aria-current="true"
                                    aria-label="Cover Image"></button>
                                @foreach ($property->images as $index => $image)
                                    <button type="button" data-bs-target="#carousel-{{ $property->id }}"
                                        data-bs-slide-to="{{ $index + 1 }}"
                                        aria-label="Slide {{ $index + 2 }}"></button>
                                @endforeach
                            </div>

                            <!-- Carousel images -->
                            <div class="carousel-inner rounded overflow-hidden" style="height: 280px;">
                                <!-- Cover Image as the First Slide -->
                                <div class="carousel-item active">
                                    <img src="{{ $property->cover_image_url }}" class="d-block w-100"
                                        style="object-fit: cover; height: 280px; border-radius: 20px;" alt="{{ $property->title }} Cover Image">
                                </div>

                                <!-- Additional Images from the "images" table -->
                                @foreach ($property->images as $image)
                                    <div class="carousel-item">
                                        <img src="{{ $image->path }}" class="d-block w-100 h-100"
                                            style="object-fit: cover;" alt="Property Image">
                                    </div>
                                @endforeach
                            </div>

                            <!-- Carousel Controls -->
                            <button class="carousel-control-prev" type="button"
                                data-bs-target="#carousel-{{ $property->id }}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button"
                                data-bs-target="#carousel-{{ $property->id }}" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>

                        <!-- Property Details -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $property->title }}</h5>
                            <p class="card-text">{{ Str::limit($property->description, 60) }}</p>
                            <p class="card-text"><strong>Price:</strong>
                                {{ number_format($property->price, 2, ',', '') }}&euro;</p>
                            <p class="card-text"><strong>Location:</strong> {{ $property->address }}</p>
                            @if (isset($property->distance))
                                <p class="card-text"><strong>Distance:</strong> {{ $property->distance }} km</p>
                            @endif
                            <a href="{{ route('properties.show', ['slug' => $property->slug]) }}"
                                class="mt-auto btn btn-custom">View Details</a>
                        </div>
                    </div>
                </div>
            @empty
                <p>No properties found within the specified criteria.</p>
            @endforelse
        </div>
    </div> --}}

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content">
                    @guest
                        <section class="p-5 rounded text-center">
                            <h1>Gestisci i tuoi immobili in modo semplice.</h1>
                        </section>
                
                        <!-- Spiegazione Gestionale -->
                        <section class="mt-2">
                            <h2 class="mb-3">Cosa fa BoolBnB</h2>
                            <p>BoolBnB è una piattaforma completa per la gestione di proprietà immobiliari. Permette ai proprietari di:</p>
                            <ul class="list-group border-0">
                                <li class="list-group-item">Registrare e gestire i propri immobili in modo semplice e veloce.</li>
                                <li class="list-group-item">Sponsorizzare gli annunci per ottenere maggiore visibilità sulla piattaforma.</li>
                                <li class="list-group-item">Monitorare le performance delle proprie proprietà tramite statistiche dettagliate.</li>
                            </ul>
                            <p class="mt-3">Gli utenti interessati possono facilmente cercare, visualizzare e contattare i proprietari per maggiori dettagli.</p>
                        </section>
                
                        <!-- Gestisci i tuoi immobili -->
                        <section class="mt-5">
                            <h2 class="mb-3">Gestisci i tuoi immobili</h2>
                            <p>Registra il tuo account e inserisci le tue proprietà. Sponsorizza i tuoi annunci per maggiore visibilità. Accedi alle statistiche per monitorare le performance.</p>
                            <a href="/register" class="btn btn-primary">Inizia Ora</a>
                        </section>
                    @else
                        <div class="text-center">
                            <h1>Bentornato {{ Auth::user()->name }}</h1>
                        </div>
                    @endguest
                    <h2>Descrizione Gestionale</h2>
                </div>
            </div>
        </div>
    </div>
@endsection

