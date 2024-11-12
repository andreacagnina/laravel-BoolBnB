@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="my-4">Messages</h1>

        <!-- Verifica se ci sono messaggi -->
        @if ($messages->isEmpty())
            <p class="text-center">No messages found.</p>
        @else
            <!-- Tabella dei messaggi -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Property Name</th>
                            <th>Property Image</th> <!-- Nuova colonna per l'immagine -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages as $message)
                            <tr>
                                <td>{{ $message->first_name }}</td>
                                <td>{{ $message->last_name }}</td>
                                <td>{{ $message->email }}</td>
                                <td>{{ Str::limit($message->message, 50) }}</td>
                                <td>{{ $message->property->title ?? 'N/A' }}</td>
                                <td>
                                    <!-- Mostra l'immagine della proprietà, se disponibile -->
                                    @if (!empty($message->property->cover_image))
                                        <img src="{{ $message->property->cover_image }}" alt="Property Image" width="100"
                                            height="60">
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.messages.show', $message->id) }}"
                                            class="btn btn-primary btn-sm">View</a>
                                        <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete"
                                                data-messageID="{{ $message->id }}">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @include('admin.properties.partials.modal_delete')
@endsection
