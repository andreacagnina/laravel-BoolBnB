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
                    <li class="list-group-item"><strong>Email:</strong> {{ $message->email }}</li>
                    <li class="list-group-item"><strong>Message:</strong> {{ $message->message }}</li>
                    <li class="list-group-item"><strong>Property ID:</strong>
                        {{ $message->property_id }}
                    </li>
                    <li class="list-group-item"><strong>Created At:</strong> {{ $message->created_at->format('d-m-Y H:i') }}
                    </li>
                </ul>

                <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary mt-3">Back to Messages</a>
            </div>
        </div>
    </div>
@endsection