@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="row text-center mb-4">
            <div class="col-12">
                <h2 class="display-6">Our Services</h2>
                <p class="text-muted">Explore the amenities available with each property</p>
            </div>
        </div>

        <div class="row">
            @foreach ($services as $service)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="icon-display mb-3" style="font-size: 2rem; color: #007bff;">
                                <i class="{{ $service->icon }}"></i> <!-- Icona di Font Awesome -->
                            </div>
                            <h5 class="card-title">{{ $service->name }}</h5>
                            <p class="card-text text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
