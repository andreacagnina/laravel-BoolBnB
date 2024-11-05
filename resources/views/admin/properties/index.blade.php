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
                            <h2>Uploaded Properties</h2>
                            <div>
                                <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">Add a new
                                    Property</a>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped align-middle table-sm text-center">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Cover Image</th>
                                    <th>Address</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Sponsored</th>
                                    <th>Available</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($properties as $property)
                                    <tr>
                                        <td>{{ $property->title }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center">
                                                @if (Str::startsWith($property->cover_image, 'https'))
                                                    <img src="{{ $property->cover_image }}" alt="{{ $property->name }}"
                                                        class="img-thumbnail"
                                                        style="width: 100px; height: 100px; object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('storage/' . $property->cover_image) }}"
                                                        alt="{{ $property->name }}" class="img-thumbnail"
                                                        style="width: 100px; height: 100px; object-fit: cover;">
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $property->address }}</td>
                                        <td>{{ $property->price }}€</td>
                                        <td>{{ Str::limit($property->description, 50) }}</td>
                                        <td>{!! $property->sponsored
                                            ? '<span class="text-success">&check;</span>'
                                            : '<span class="text-danger">&cross;</span>' !!}</td>
                                        <td>{!! $property->available
                                            ? '<span class="text-success">&check;</span>'
                                            : '<span class="text-danger">&cross;</span>' !!}</td>
                                        <td>
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('admin.sponsors.show', ['property' => $property->slug]) }}"
                                                    class="btn btn-outline-success mx-1">
                                                    <i class="fas fa-money-bill"></i>
                                                </a>
                                                <a href="{{ route('admin.views.show', ['property' => $property->slug]) }}"
                                                    class="btn btn-outline-info mx-1">
                                                    <i class="fas fa-chart-pie"></i>
                                                </a>
                                                <a href="{{ route('admin.properties.show', ['property' => $property->slug]) }}"
                                                    class="btn btn-outline-primary mx-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.properties.edit', ['property' => $property->slug]) }}"
                                                    class="btn btn-outline-warning mx-1">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <form
                                                    action="{{ route('admin.properties.destroy', ['property' => $property->slug]) }}"
                                                    method="post" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger mx-1 delete"
                                                        data-propertyName="{{ $property->title }}">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="my-3 d-flex justify-content-between align-items-center">
                            <h2>You haven't uploaded any properties yet</h2>
                            <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">Add a new Property</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('admin.properties.partials.modal_delete')
@endsection
