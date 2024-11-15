@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Message Details</h1>
        <div class="card">
            <div class="card-header">
                Message from @if($message->first_name == null && $message->last_name == null)
                <td>N/A</td>
                @else
                {{ $message->first_name }} {{ $message->last_name }}
                @endif
            </div>
            <div class="card-body">
                <h5 class="card-title">Details</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>First Name:</strong> @if($message->first_name == null) N/A
                        @else{{ $message->first_name }}@endif</li>
                    <li class="list-group-item"><strong>Last Name:</strong> @if($message->last_name == null) N/A
                        @else{{ $message->last_name }}@endif</li>
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
