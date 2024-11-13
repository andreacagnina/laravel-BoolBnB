@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="my-4">Messages</h1>
        @if (session('success'))
            <div class="row">
                <div class="col-12">
                    <div class="content mt-1 text-center position-relative">
                        <div id="success-alert" class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Verifica se ci sono messaggi -->
        @if ($messages->isEmpty())
            <p class="text-center">No messages found.</p>
        @else
            <!-- Tabella dei messaggi -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle text-center table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Received</th>
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
                                <td>{{ $message->created_at->diffForHumans() }}</td>
                                <td>{{ $message->first_name }}</td>
                                <td>{{ $message->last_name }}</td>
                                <td><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></td>
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
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.messages.show', $message->id) }}"
                                            class="btn btn-primary btn-sm">View</a>
                                        <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete">Delete</button>
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
