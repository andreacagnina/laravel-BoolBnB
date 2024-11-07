@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content p-4 border rounded shadow-sm bg-light">
                    <h3 class="mb-4 text-center">Select a Sponsor and a Property</h3>

                    <!-- Form to select sponsor and property -->
                    <form action="{{ route('admin.properties.assignSponsor') }}" method="POST" id="assignSponsorForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="property_slug" class="form-label">Select a Property:</label>
                            <select name="property_slug" id="property_slug" class="form-select" required>
                                <option value="" disabled selected>-- Select a Property --</option>
                                @foreach ($properties->sortBy('title') as $property)
                                    <option value="{{ $property->slug }}">
                                        {{ $property->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="sponsor_id" class="form-label">Select a Sponsor:</label>
                            <select name="sponsor_id" id="sponsor_id" class="form-select" required>
                                <option value="" disabled selected>-- Select a Sponsor --</option>
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
            </div>
        </div>
    </div>
@endsection
