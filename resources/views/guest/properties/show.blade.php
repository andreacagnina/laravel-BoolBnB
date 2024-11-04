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
                            <input type="hidden" id="lat" value="{{ $property->lat }}">
                            <input type="hidden" id="long" value="{{ $property->long }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h4 class="mb-4">Informazioni sulla proprietà</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Descrizione:</strong> {{ $property->description }}</li>
                            <li class="list-group-item"><strong>Indirizzo:</strong> {{ $property->address }}</li>
                            <li class="list-group-item"><strong>Piano:</strong> {{ $property->floor }}</li>
                            <li class="list-group-item"><strong>Prezzo:</strong> {{ $property->price }}€</li>
                            <li class="list-group-item"><strong>Metri quadri:</strong> {{ $property->mq }} mq</li>
                            <li class="list-group-item"><strong>Numero di stanze:</strong> {{ $property->num_rooms }}</li>
                            <li class="list-group-item"><strong>Numero di letti:</strong> {{ $property->num_beds }}</li>
                            <li class="list-group-item"><strong>Numero di bagni:</strong> {{ $property->num_baths }}</li>
                            <li class="list-group-item"><strong>Tipo:</strong>
                                {{ ucfirst(str_replace('-', ' ', $property->type)) }}</li>
                            <li class="list-group-item"><strong>Servizi disponibili:</strong>
                                @if ($property->services->isEmpty())
                                    Nessun servizio incluso.
                                @else
                                    @foreach ($property->services as $service)
                                        <span>{{ $service->name }} <i class="{{ $service->icon }} me-2"></i></span>
                                    @endforeach
                                @endif
                            </li>
                            <li class="list-group-item"><strong>Disponibilità:</strong>
                                {{ $property->available ? 'Sì' : 'No' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('homepage') }}" class="btn btn-secondary">Torna alla home</a>
            </div>
        </div>
    </div>
@endsection
