@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="my-4">Message Details</h1>

        <div class="card">
            <div class="card-header">
                Message from {{ $message->first_name }} {{ $message->last_name }}
            </div>
            <div class="card-body">
                <h5 class="card-title">Details</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>First Name:</strong> {{ $message->first_name }}</li>
                    <li class="list-group-item"><strong>Last Name:</strong> {{ $message->last_name }}</li>
                    <li class="list-group-item"><strong>Email:</strong> <a
                            href="mailto:{{ $message->email }}">{{ $message->email }}</a> </li>
                    <li class="list-group-item"><strong>Message:</strong> {{ $message->message }}</li>
                    <li class="list-group-item"><strong>Property Name:</strong> {{ $message->property->title ?? 'N/A' }}
                    </li>

                    <!-- Aggiungi l'immagine della proprietà se esiste -->
                    <li class="list-group-item">
                        <strong>Property Image:</strong><br>
                        @if (!empty($message->property->cover_image))
                            <img src="{{ $message->property->cover_image }}" alt="Property Image" width="200">
                        @else
                            N/A
                        @endif
                    </li>
                </ul>

                <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary mt-3">Back to Messages</a>
            </div>
        </div>
    </div>
@endsection
