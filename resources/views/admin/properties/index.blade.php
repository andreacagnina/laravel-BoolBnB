@extends('layouts.app')
@section('content')
    <div class="container my-3">
        <div class="row">
            <div class="col-12">
                <div class="content">
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
                    @if (count($properties) != 0)
                        <div class="my-3 d-flex justify-content-between align-items-center">
                            <h2>Proprietà caricate</h2>
                            <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">Add a new Property</a>
                        </div>
                        <table class="table table-bordered table-striped align-middle table-sm text-center">
                            <thead>
                                <tr>
                                    <th>Titolo</th>
                                    <th>Immagine di Copertina</th>
                                    <th>Indirizzo</th>
                                    <th>Prezzo</th>
                                    <th>Descrizione</th>
                                    <th>Strumenti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($properties as $property)
                                    <tr>
                                        <td>{{ $property->title }}</td>
                                        <td>
                                            @if (Str::startsWith($property->cover_image, 'https'))
                                                <img class="cover_image w_100" src="{{ $property->cover_image }}"
                                                    alt="{{ $property->name }}">
                                            @else
                                                <img class="cover_image w_100"
                                                    src="{{ asset('storage/' . $property->cover_image) }}"
                                                    alt="{{ $property->name }}">
                                            @endif
                                        </td>
                                        <td>{{ $property->address }}</td>
                                        <td>{{ $property->price }}€</td>
                                        <td>{{ Str::limit($property->description, 50) }}</td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('admin.sponsors.index', ['property' => $property->id]) }}"
                                                    class="btn btn-outline-success mx-1">
                                                    <i class="fas fa-money-bill"></i>
                                                </a>
                                                <a href="{{ route('admin.views.show', ['property' => $property->id]) }}"
                                                    class="btn btn-outline-info mx-1">
                                                    <i class="fas fa-chart-pie"></i>
                                                </a>
                                                <a href="{{ route('admin.properties.show', ['property' => $property->id]) }}"
                                                    class="btn btn-outline-primary mx-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a class="btn btn-outline-warning mx-1"
                                                    href="{{ route('admin.properties.edit', ['property' => $property->id]) }}"><i
                                                        class="fa-solid fa-pen-to-square"></i></a>
                                                </a>
                                                <form
                                                    action="{{ route('admin.properties.destroy', ['property' => $property->id]) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger delete"
                                                        data-propertyName="{{ $property->title }}"><i
                                                            class="fa-solid fa-trash"></i>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="my-3 d-flex justify-content-between align-items-center">
                            <h2>Non hai ancora caricato nessun immobile</h2>
                            <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">Add a new Property</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('admin.properties.partials.modal_delete')
@endsection
