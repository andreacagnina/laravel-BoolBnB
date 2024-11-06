@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content p-4 border rounded shadow-sm bg-light">
                    <h3 class="mb-4 text-center">Seleziona uno Sponsor e una Proprietà</h3>

                    <!-- Form per selezionare sponsor e proprietà -->
                    <form action="{{ route('admin.properties.assignSponsor') }}" method="POST" id="assignSponsorForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="property_slug" class="form-label">Seleziona una Proprietà:</label>
                            <select name="property_slug" id="property_slug" class="form-select" required>
                                <option value="" disabled selected>-- Seleziona una Proprietà --</option>
                                @foreach ($properties as $property)
                                    <option value="{{ $property->slug }}">
                                        {{ $property->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="sponsor_id" class="form-label">Seleziona uno Sponsor:</label>
                            <select name="sponsor_id" id="sponsor_id" class="form-select" required>
                                <option value="" disabled selected>-- Seleziona uno Sponsor --</option>
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
            </div>
        </div>
    </div>
@endsection
