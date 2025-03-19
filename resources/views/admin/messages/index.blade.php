@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-center mb-4">Messages</h1>
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

        @if ($messages->isEmpty() || $messages->filter(fn($message) => $message->deleted_at === null)->isEmpty())
            <p class="text-center">No messages found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle text-center table-sm">
                    <thead class="table-light fw-bold">
                        <tr>
                            <th>Received</th>
                            <th>Sended By</th>
                            <th>Email</th>
                            <th>Property Name</th>
                            <th>Property Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($messages->filter(fn($message) => $message->deleted_at === null) as $message)
                            <tr class="message-row {{ $message->is_read ? '' : 'unread' }}" data-href="{{ route('admin.messages.show', $message->id) }}">
                                <td class="position-relative">
                                    @if (!$message->is_read)
                                        <span class="unread-dot"></span>
                                    @endif
                                    {{ $message->created_at->diffForHumans() }}
                                </td>
                                <td>{{ $message->first_name ?? 'N/A' }} {{ $message->last_name ?? '' }}</td>
                                <td>{{ $message->email }}</td>
                                <td>{{ $message->property->title ?? 'N/A' }}</td>
                                <td>
                                    @if (!empty($message->property->cover_image))
                                        <img src="{{ $message->property->cover_image }}" alt="Property Image" class="img-fluid" style="max-width: 100px; height: auto;">
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center flex-wrap gap-2">
                                        <a href="{{ route('admin.messages.show', $message->id) }}" class="btn btn-primary btn-sm btn-search">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </a>
                                        <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm delete">
                                                <i class="fa-solid fa-trash"></i>
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

    <style>
        /* Sfondo più chiaro per messaggi non letti */
        tr.unread {
            background-color: #ffffff26;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
        }

        /* Pallino rosso per messaggi non letti */
        .unread-dot {
            position: absolute;
            top: 10px;
            left: 5px;
            transform: translateY(-50%);
            width: 10px;
            height: 10px;
            background-color: #dc3545;
            border-radius: 50%;
            box-shadow: 0 0 5px rgba(220, 53, 69, 0.7);
        }

        @media (max-width: 767px) {
            table {
                font-size: 12px;
            }

            th, td {
                font-size: 10px;
                padding: 4px;
                word-wrap: break-word;
            }

            th:nth-child(4), td:nth-child(4) {
                display: none; /* Nasconde la colonna "Property Name" su schermi piccoli */
            }

            th:nth-child(5), td:nth-child(5) {
                display: none; /* Nasconde la colonna "Property Image" su schermi piccoli */
            }

            .btn {
                font-size: 10px;
                padding: 4px 6px;
            }

            .unread-dot {
                width: 8px;
                height: 8px;
            }

            /* Nasconde il bottone della ricerca */
            .btn-search {
                display: none;
            }

            .unread-dot {
            top: 7px;
            left: 2px;
            }
        }
    </style>

    <script>
        // Rendi la riga cliccabile
        document.querySelectorAll('.message-row').forEach(row => {
            row.addEventListener('click', () => {
                window.location.href = row.dataset.href;
            });
        });

        // Evita che il click sui pulsanti interni venga intercettato dalla riga
        document.querySelectorAll('.btn-search, .delete').forEach(button => {
            button.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });
    </script>
@endsection
