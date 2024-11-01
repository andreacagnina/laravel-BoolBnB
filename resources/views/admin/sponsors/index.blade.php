@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content">
                    <ul>
                        @foreach ($sponsors as $sponsor)
                            <li>{{ $sponsor->name }} {{ $sponsor->price }} {{ $sponsor->duration }} ore</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
