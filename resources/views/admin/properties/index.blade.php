@extends('layouts.app')
@section('content')
    <div class="container text-bg-dark my-3">
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
