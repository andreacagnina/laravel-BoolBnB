@extends('layouts.app')
@section('content')
    <div class="container my-3">
        <div class="row">
            <div class="col-12">
                <h2>Statistiche delle Visualizzazioni</h2>
                <div class="card mb-3">
                    <div class="card-header">
                        <h3>{{ $property->title }}</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Indirizzo:</strong> {{ $property->address }}</p>
                        <p><strong>Prezzo:</strong> {{ $property->price }} €</p>
                        <p><strong>Descrizione:</strong> {{ $property->description }}</p>
                        <p><strong>Visualizzazioni Totali:</strong> {{ $property->views_count }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.properties.index') }}" class="btn btn-primary">Torna alla lista delle proprietà</a>
            </div>
        </div>
    </div>
@endsection
