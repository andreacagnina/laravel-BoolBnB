@extends('layouts.app')
@section('content')
    <div class="container">
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

                    @if ($properties->isNotEmpty())
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h1>My Properties</h1>
                            <a href="{{ route('admin.properties.create') }}" class="btn btn-primary my-2">
                                Add a new Property
                            </a>
                        </div>
                        <div class="row">
                            {{-- Proprietà attive --}}
                            @foreach ($properties->where('deleted_at', null) as $property)
                                <div class="col-12 col-md-6 col-lg-4 my-4">
                                    <div class="card h-100">
                                        <div class="card-header cust text-center">
                                            <h5 class="fw-bold">{{ $property->title }}</h5>
                                        </div>
                                        <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                                            alt="{{ $property->name }}" class="card-img-top p-0 rounded "
                                            style="height: 200px; object-fit: cover;">
                                        <div class="card-body">
                                            <p><strong>Address:</strong> {{ $property->address }}</p>
                                            <p><strong>Price:</strong> {{ number_format($property->price, 2, ',', '') }}&euro;</p>
                                            <p><strong>Description:</strong> {{ Str::limit($property->description, 50) }}</p>
                                            <p>
                                                <strong>Sponsored:</strong>
                                                <i class="ms-2 {{$property->sponsored ? 'fa-solid fa-check text-success' : 'fa-solid fa-x text-danger'}}"></i>
                                            </p>
                                            <p>
                                                <strong>Available:</strong>
                                                <i class="ms-2 {{$property->available ? 'fa-solid fa-check text-success' : 'fa-solid fa-x text-danger'}}"></i>
                                            </p>
                                        </div>
                                        <div class="card-footer d-flex justify-content-around">
                                            <a href="{{ route('admin.sponsors.property_show', ['property' => $property->slug]) }}"
                                                class="btn btn-success">
                                                <i class="fas fa-money-bill"></i>
                                            </a>
                                            <a href="{{ route('admin.views.show', ['property' => $property->slug]) }}"
                                                class="btn btn-info">
                                                <i class="fas fa-chart-pie"></i>
                                            </a>
                                            <a href="{{ route('admin.properties.show', ['property' => $property->slug]) }}"
                                                class="btn btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.properties.edit', ['property' => $property->slug]) }}"
                                                class="btn btn-warning">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ route('admin.properties.destroy', ['property' => $property->slug]) }}"
                                                method="post" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Proprietà eliminate --}}
                            @foreach ($properties->where('deleted_at', '!=', null) as $property)
                                <div class="col-12 col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100" style="position: relative; border-radius: 5px;">
                                        <!-- La parte sopra oscurata -->
                                        <div style="background: rgba(0, 0, 0, 0.4); border-radius: 5px 5px 0 0;">
                                            <div class="card-header text-center">
                                                <h5>{{ $property->title }}</h5>
                                            </div>
                                            <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                                                alt="{{ $property->name }}" class="card-img-top img-thumbnail p-0 rounded-0"
                                                style="height: 200px; object-fit: cover; filter: grayscale(100%) brightness(50%); z-index: 0;">
                                            <div class="card-body" style="z-index: 1;">
                                                <p><strong>Address:</strong> {{ $property->address }}</p>
                                                <p><strong>Price:</strong> {{ number_format($property->price, 2, ',', '') }}&euro;</p>
                                                <p><strong>Description:</strong> {{ Str::limit($property->description, 50) }}</p>
                                            </div>
                                        </div>

                                        <!-- Sezione inferiore con messaggio di eliminazione e pulsante di restore -->
                                        <div class="card-footer d-flex justify-content-between align-items-center" style="background-color: #f25e6c; border-radius: 0 0 5px 5px;">
                                            <p class="mb-0">This property has been deleted</p>
                                            <form action="{{ route('admin.properties.restore', ['id' => $property->id]) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-undo"></i> Restore
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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


