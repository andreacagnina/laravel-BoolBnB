@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="content">
                    <ul>
                        @foreach ($services as $service)
                            <li>{{ $service->name }} <i class="{{ $service->icon }}"></i></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
