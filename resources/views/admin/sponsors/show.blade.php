@extends('layouts.app')

@section('content')
    <div class="container py-4">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <!-- Dettagli della proprietà e sponsorizzazione esistente -->
        <h3 class="mb-4">Sponsorship Details for Apartment: {{ $property->title }}</h3>
        @if ($property->sponsors->isNotEmpty())
            @php
                // Ottieni l'ultima sponsorizzazione attiva ordinando per la data di creazione in ordine decrescente
$latestSponsor = $property->sponsors->sortByDesc('pivot.created_at')->first();
            @endphp

            <h4>Latest Sponsor: {{ $latestSponsor->name }} for {{ $latestSponsor->price }} &euro;</h4>
            <h4>End Sponsor Date:
                {{ $latestSponsor->pivot->end_date ? \Carbon\Carbon::parse($latestSponsor->pivot->end_date)->format('d-m-Y H:i') : 'N/A' }}
            </h4>
        @else
            <p>No active sponsorships available for this property.</p>
        @endif
        <!-- Aggiungi una nuova sponsorizzazione -->
        <h4 class="mt-5 mb-3">Add a New Sponsorship</h4>

        <form id="payment-form" action="{{ route('admin.braintree.checkout') }}" method="POST" class="border p-4 rounded">
            @csrf
            <input type="hidden" name="property_slug" value="{{ $property->slug }}">
            <input type="hidden" name="sponsor_id" id="sponsor_id">
            <input type="hidden" name="payment_method_nonce" id="payment-method-nonce">

            <div class="form-group mb-3">
                <label for="sponsor">Select a Sponsor:</label>
                <select id="sponsor-select" class="form-select" required>
                    <option value="" selected disabled>Select a Sponsor</option>
                    @foreach ($sponsors as $sponsor)
                        <option value="{{ $sponsor->id }}" data-price="{{ $sponsor->price }}">
                            {{ $sponsor->name }} - {{ number_format($sponsor->price, 2, ',', '') }}&euro; for
                            {{ $sponsor->duration }} hours
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="dropin-container"></div>
            <div class="text-end d-flex justify-content-end align-items-center gap-2">
                <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Go Back</a>
                <button type="button" id="pay-button" class="btn btn-primary px-4">Pay with Braintree</button>
            </div>
        </form>
    </div>
@endsection
