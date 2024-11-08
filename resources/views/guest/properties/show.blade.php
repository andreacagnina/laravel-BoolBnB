@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="card shadow-lg">
            {{-- Card Header --}}
            <div class="card-header bg-primary text-white text-center">
                <h2 class="mb-0">{{ $property->title }}</h2>
            </div>

            {{-- Card Body --}}
            <div class="card-body">
                <div class="row">
                    {{-- Left Column: Carousel and Map --}}
                    <div class="col-lg-6">
                        {{-- Carousel for Property Images --}}
                        <div id="propertyImagesCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                            {{-- Carousel Indicators (Dots only) --}}
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#propertyImagesCarousel" data-bs-slide-to="0"
                                    class="active" aria-current="true" aria-label="Cover Image"></button>
                                @foreach ($property->images as $index => $image)
                                    <button type="button" data-bs-target="#propertyImagesCarousel"
                                        data-bs-slide-to="{{ $index + 1 }}" class=""
                                        aria-label="Slide {{ $index + 2 }}"></button>
                                @endforeach
                            </div>

                            {{-- Carousel Inner --}}
                            <div class="carousel-inner border rounded shadow-sm">
                                {{-- Cover Image as the First Slide --}}
                                <div class="carousel-item active">
                                    <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                                        class="d-block w-100 rounded" alt="Cover Image"
                                        style="height: 400px; object-fit: cover;">
                                </div>

                                {{-- Additional Images from the "images" Table --}}
                                @foreach ($property->images as $image)
                                    <div class="carousel-item">
                                        <img src="{{ $image->path }}" class="d-block w-100 rounded" alt="Property Image"
                                            style="height: 400px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>

                            {{-- Carousel Controls --}}
                            <button class="carousel-control-prev" type="button" data-bs-target="#propertyImagesCarousel"
                                data-bs-slide="prev" style="width: 5%;">
                                <span class="carousel-control-prev-icon" aria-hidden="true" style="font-size: 2rem;"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#propertyImagesCarousel"
                                data-bs-slide="next" style="width: 5%;">
                                <span class="carousel-control-next-icon" aria-hidden="true" style="font-size: 2rem;"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>

                        {{-- Map Section --}}
                        <div id="map" class="rounded mb-3" style="width: 100%; height: 300px;">
                            <input type="hidden" id="lat" value="{{ $property->lat }}">
                            <input type="hidden" id="long" value="{{ $property->long }}">
                        </div>
                    </div>

                    {{-- Right Column: Property Details --}}
                    <div class="col-lg-6">
                        <h4 class="mb-4">Property Information</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Description:</strong> {{ $property->description }}</li>
                            <li class="list-group-item"><strong>Address:</strong> {{ $property->address }}</li>
                            <li class="list-group-item"><strong>Floor:</strong> {{ $property->floor }}</li>
                            <li class="list-group-item"><strong>Price:</strong>
                                {{ number_format($property->price, 2, ',', '') }}&euro;</li>
                            <li class="list-group-item"><strong>Square Meters:</strong> {{ $property->mq }} sqm</li>
                            <li class="list-group-item"><strong>Number of Rooms:</strong> {{ $property->num_rooms }}</li>
                            <li class="list-group-item"><strong>Number of Beds:</strong> {{ $property->num_beds }}</li>
                            <li class="list-group-item"><strong>Number of Bathrooms:</strong> {{ $property->num_baths }}
                            </li>
                            <li class="list-group-item"><strong>Type:</strong>
                                {{ ucfirst(str_replace('-', ' ', $property->type)) }}</li>
                            <li class="list-group-item"><strong>Available Services:</strong>
                                @if ($property->services->isEmpty())
                                    No services included.
                                @else
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($property->services as $service)
                                            <li>{{ $service->name }} <i class="{{ $service->icon }} ms-2"></i></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                            <li class="list-group-item"><strong>Availability:</strong>
                                {{ $property->available ? 'Yes' : 'No' }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Card Footer --}}
            <div class="card-footer text-end">
                <a href="{{ route('homepage') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
@endsection
