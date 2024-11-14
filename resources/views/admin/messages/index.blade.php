@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="my-4 fw-bold text-center">Messages</h1>
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
        @if ($messages->isEmpty() || $messages->filter(fn($message) => $message->deleted_at === null)->isEmpty())
            <p class="text-center">No new messages found.</p>
        @else
            <!-- Tabella dei messaggi -->
            <div class="table-responsive">
                <h2 class="fw-bold">Inbox</h2>
                <table class="table table-bordered table-striped align-middle text-center table-sm">
                    <thead class="table-light fw-bold">
                        <tr>
                            <th>Received</th>
                            <th>Sended By</th>
                            <th>Email</th>
                            <th>Property Name</th>
                            <th>Property Image</th> <!-- Nuova colonna per l'immagine -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages->filter(fn($message) => $message->deleted_at === null) as $message)
                            <tr class="{{ $message->is_read ? 'fw-normal' : 'fw-bold' }}">
                                <td>{{ $message->created_at->diffForHumans() }}</td>
                                <td>{{ $message->first_name }} {{ $message->last_name }}</td>
                                {{-- <td>{{ $message->last_name }}</td> --}}
                                <td><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></td>
                                {{-- <td>{{ Str::limit($message->message, 50) }}</td> --}}
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
                                            class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass"></i></a>
                                        <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete"><i
                                                    class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        @if ($messages->filter(fn($message) => $message->deleted_at != null)->isNotEmpty())
            <div class="table-responsive">
                <h2>Trashed</h2>
                <table class="table table-bordered table-striped align-middle text-center table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Received</th>
                            <th>Sended By</th>
                            <th>Email</th>
                            <th>Property Name</th>
                            <th>Property Image</th> <!-- Nuova colonna per l'immagine -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages->filter(fn($message) => $message->deleted_at !== null) as $message)
                            <tr>
                                <td>{{ $message->created_at->diffForHumans() }}</td>
                                <td>{{ $message->first_name }} {{ $message->last_name }}</td>

                                <td><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></td>

                                <td>{{ $message->property->title ?? 'N/A' }}</td>
                                <td>
                                    <!-- Mostra l'immagine della proprietà, se disponibile -->
                                    @if (!empty($message->property->cover_image))
                                        <img src="{{ $message->property->cover_image }}" alt="Property Image"
                                            width="100" height="60" style="filter: grayscale(100%);">
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <form action="{{ route('admin.messages.restore', ['id' => $message->id]) }}"
                                            method="post" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.messages.hardDestroy', ['id' => $message->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete"><i
                                                    class="fa-solid fa-trash"></i>
                                            </button>
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
