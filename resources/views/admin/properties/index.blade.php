@extends('layouts.app')
@section('content')
    <div class="container my-3">
        <div class="row">
            <div class="col-12">
                <div class="content">
                    <ul>
                        @if (count($properties) != 0)
                            @foreach ($properties as $property)
                                <li>{{ $property->title }}
                                    <img src="{{ $property->cover_image }}" alt="cover_image">
                                </li>
                            @endforeach
                        @else
                            <h2>Non hai ancora caricato nessun immobile</h2>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
