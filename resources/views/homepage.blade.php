@extends('layouts.app')
@section('content')
    <div class="container my-3">
        <div class="row">
            <div class="col-12">
                <div class="content">
                    <h1>Welcome in BoolBnB</h1>
                    <h2>//PER LA VISTA UTENTE SI POTREBBERO REALIZZARE DELLE CARDS</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="content">
                    <ul class="d-flex flex-column">
                        @foreach ($properties as $property)
                            <li class="{{ $property->sponsored ? 'order-0 text-success' : 'order-1' }}">
                                {{ $property->title }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
