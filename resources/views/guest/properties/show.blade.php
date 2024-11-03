@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2>{{ $property->title }}</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                            class="img-fluid rounded mb-3" alt="{{ $property->title }}">
                    </div>
                    <div class="col-md-6">
                        <h4>Informazioni sulla proprietà</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Prezzo:</strong> €{{ number_format($property->price, 2) }}
                            </li>
                            <li class="list-group-item"><strong>Indirizzo:</strong> {{ $property->address }}</li>
                            <li class="list-group-item"><strong>Metri quadri:</strong> {{ $property->mq }} mq</li>
                            <li class="list-group-item"><strong>Numero di stanze:</strong> {{ $property->num_rooms }}</li>
                            <li class="list-group-item"><strong>Numero di letti:</strong> {{ $property->num_beds }}</li>
                            <li class="list-group-item"><strong>Numero di bagni:</strong> {{ $property->num_baths }}</li>
                            <li class="list-group-item"><strong>Piano:</strong> {{ $property->floor }}</li>
                            <li class="list-group-item"><strong>Disponibilità:</strong>
                                {{ $property->available ? 'Disponibile' : 'Non disponibile' }}</li>
                        </ul>
                    </div>
                </div>
                <hr>
                <h5>Descrizione</h5>
                <p>{{ $property->description }}</p>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('homepage') }}" class="btn btn-secondary">Torna alla home</a>
            </div>
        </div>
    </div>
@endsection
