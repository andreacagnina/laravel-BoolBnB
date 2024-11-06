@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h2>{{ $property->title }}</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                            class="img-fluid rounded mb-3" alt="{{ $property->title }}">
                        <div id="map" class="rounded mb-3" style="width: 100%; height: 300px;">
                        </div>
                        <input type="hidden" id="lat" value="{{ $property->lat }}">
                        <input type="hidden" id="long" value="{{ $property->long }}">
                    </div>
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
                                    @foreach ($property->services as $service)
                                        <span>{{ $service->name }} <i class="{{ $service->icon }} me-2"></i></span>
                                    @endforeach
                                @endif
                            </li>
                            <li class="list-group-item"><strong>Availability:</strong>
                                {{ $property->available ? 'Yes' : 'No' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Back to list</a>
            </div>
        </div>
    </div>
@endsection
