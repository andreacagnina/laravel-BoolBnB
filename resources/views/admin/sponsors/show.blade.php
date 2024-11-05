@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <h3 class="mb-4">Sponsorship Details for Apartment: {{ $property->title }}</h3>

        <div class="mb-3">
            <p>{{ $property->description }}</p>
            <p><strong>Type:</strong> {{ $property->type }}</p>
            <p><strong>Price:</strong> €{{ $property->price }}</p>
        </div>

        <h4 class="mt-4 mb-3">Active Sponsors</h4>

        @if ($property->sponsors->isEmpty())
            <p>This apartment has no active sponsorships.</p>
        @else
            <div class="list-group mb-4">
                @foreach ($property->sponsors as $sponsor)
                    @php
                        // Calculate end date by adding duration to the start date
                        $endDate = $sponsor->pivot->created_at->copy()->addHours($sponsor->duration);
                    @endphp
                    <div class="list-group-item mb-3">
                        <p><strong>Sponsor:</strong> {{ $sponsor->name }}</p>
                        <p><strong>Price:</strong> €{{ $sponsor->price }}</p>
                        <p><strong>Duration:</strong> {{ $sponsor->duration }} hours</p>
                        <p><strong>Start Date:</strong>
                            {{ $sponsor->pivot->created_at ? $sponsor->pivot->created_at->format('d-m-Y H:i') : 'Date not available' }}
                        </p>
                        <p><strong>End Date:</strong>
                            {{ $sponsor->pivot->created_at ? $endDate->format('d-m-Y H:i') : 'Date not available' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif

        <h4 class="mt-5 mb-3">Add a New Sponsorship</h4>

        <form action="{{ route('admin.properties.assignSponsor') }}" method="POST" class="border p-4 rounded">
            @csrf
            <input type="hidden" name="property_slug" value="{{ $property->slug }}">

            <div class="form-group mb-3">
                <label for="sponsor_id">Select a Sponsor:</label>
                <select name="sponsor_id" id="sponsor_id" class="form-select" required>
                    @foreach ($sponsors as $sponsor)
                        <option value="{{ $sponsor->id }}">
                            {{ $sponsor->name }} - €{{ $sponsor->price }} for {{ $sponsor->duration }} hours
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="text-end d-flex justify-content-end align-items-center gap-2">
                <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Go Back</a>
                <button type="submit" class="btn btn-primary px-4">Add</button>
            </div>
        </form>
    </div>
@endsection
