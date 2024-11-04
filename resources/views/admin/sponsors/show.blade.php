@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h3 class="mb-4">Dettagli Sponsorizzazione per l'Appartamento: {{ $property->title }}</h3>

        <div class="mb-3">
            <p>{{ $property->description }}</p>
            <p><strong>Tipo:</strong> {{ $property->type }}</p>
            <p><strong>Prezzo:</strong> €{{ $property->price }}</p>
        </div>

        <h4 class="mt-4 mb-3">Sponsor Attivi</h4>

        @if ($property->sponsors->isEmpty())
            <p>Questo appartamento non ha sponsorizzazioni attive.</p>
        @else
            <div class="list-group mb-4">
                @foreach ($property->sponsors as $sponsor)
                    @php
                        // Calcola la data di fine aggiungendo la durata alla data di inizio
                        $endDate = $sponsor->pivot->created_at->copy()->addHours($sponsor->duration);
                    @endphp
                    <div class="list-group-item mb-3">
                        <p><strong>Sponsor:</strong> {{ $sponsor->name }}</p>
                        <p><strong>Prezzo:</strong> €{{ $sponsor->price }}</p>
                        <p><strong>Durata:</strong> {{ $sponsor->duration }} ore</p>
                        <p><strong>Data inizio:</strong>
                            {{ $sponsor->pivot->created_at ? $sponsor->pivot->created_at->format('d-m-Y H:i') : 'Data non disponibile' }}
                        </p>
                        <p><strong>Data fine:</strong>
                            {{ $sponsor->pivot->created_at ? $endDate->format('d-m-Y H:i') : 'Data non disponibile' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif

        <h4 class="mt-5 mb-3">Aggiungi una Nuova Sponsorizzazione</h4>

        <form action="{{ route('admin.properties.assignSponsor') }}" method="POST" class="border p-4 rounded">
            @csrf
            <input type="hidden" name="property_slug" value="{{ $property->slug }}">

            <div class="form-group mb-3">
                <label for="sponsor_id">Seleziona uno Sponsor:</label>
                <select name="sponsor_id" id="sponsor_id" class="form-control" required>
                    @foreach ($sponsors as $sponsor)
                        <option value="{{ $sponsor->id }}">
                            {{ $sponsor->name }} - €{{ $sponsor->price }} per {{ $sponsor->duration }} ore
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="text-end d-flex justify-content-end align-items-center gap-2">
                <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Torna Indietro</a>
                <button type="submit" class="btn btn-primary px-4">Aggiungi</button>
            </div>
        </form>
    </div>
@endsection
