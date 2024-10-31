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
                    <ul>
                        @foreach ($properties as $property)
                            <li>{{ $property->title }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
