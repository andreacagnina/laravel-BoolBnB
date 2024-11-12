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
                            <th>Property ID</th>
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
                                <td>{{ $message->property->name }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.messages.show', $message->id) }}"
                                            class="btn btn-primary btn-sm">View</a>
                                        <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this message?')">Delete</button>
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
@endsection
