@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">{{ $property->title }}</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 text-center">
                                <img src="{{ $property->cover_image }}" class="img-fluid rounded mb-3"
                                    alt="{{ $property->title }}">
                            </div>
                            <div class="col-md-7">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>Descrizione:</strong><br>
                                        {{ $property->description }}</li>
                                    <li class="list-group-item"><strong>Indirizzo:</strong> {{ $property->address }}</li>
                                    <li class="list-group-item"><strong>Piano:</strong> {{ $property->floor }}</li>
                                    <li class="list-group-item"><strong>Prezzo:</strong> {{ $property->price }}€</li>
                                    <li class="list-group-item"><strong>Metri quadri:</strong> {{ $property->mq }} mq</li>
                                    <li class="list-group-item"><strong>Numero di stanze:</strong>
                                        {{ $property->num_rooms }}</li>
                                    <li class="list-group-item"><strong>Numero di letti:</strong> {{ $property->num_beds }}
                                    </li>
                                    <li class="list-group-item"><strong>Numero di bagni:</strong>
                                        {{ $property->num_baths }}
                                    </li>
                                    <li class="list-group-item"><strong>Tipo:</strong>
                                        {{ ucfirst(str_replace('-', ' ', $property->type)) }}</li>
                                    @if ($property->services->isEmpty())
                                        <li class="list-group-item"><strong>Servizi disponibili:</strong> Nessun servizio
                                            incluso.
                                        @else
                                        <li class="list-group-item"><strong>Servizi disponibili:</strong>
                                            @foreach ($property->services as $service)
                                                <span>
                                                    {{ $service->name }} <i class="{{ $service->icon }} me-2"></i>
                                                </span>
                                            @endforeach
                                        </li>
                                    @endif
                                    <li class="list-group-item"><strong>Disponibilità:</strong>
                                        {{ $property->available ? 'Sì' : 'No' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('admin.properties.edit', ['property' => $property->slug]) }}"
                            class="btn btn-warning">
                            <i class="fa-solid fa-pen-to-square"></i> Modifica
                        </a>
                        <form action="{{ route('admin.properties.destroy', ['property' => $property->slug]) }}"
                            method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-trash"></i> Elimina
                            </button>
                        </form>
                        <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Torna all'elenco</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
