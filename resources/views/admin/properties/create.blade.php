@extends('layouts.app')

@section('content')
    <div class="container my-3">
        <div class="row">
            <div class="col-12">
                <div class="content">
                    <h1>Aggiungi una nuova Property</h1>
                </div>
            </div>
            <div class="col">
                <form action="{{ route('admin.properties.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <label for="title" class="form-label">Titolo:</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                class="form-control @error('title') is-invalid @enderror" required>
                            @error('title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-4">
                            <label for="num_rooms" class="form-label">N. Rooms:</label>
                            <input type="number" name="num_rooms" id="num_rooms" value="{{ old('num_rooms') }}"
                                class="form-control @error('num_rooms') is-invalid @enderror" required min="1"
                                max="50">
                            @error('num_rooms')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-4">
                            <label for="num_beds" class="form-label">N. Beds:</label>
                            <input type="number" name="num_beds" id="num_beds" value="{{ old('num_beds') }}"
                                class="form-control @error('num_beds') is-invalid @enderror" required min="1"
                                max="20">
                            @error('num_beds')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-4">
                            <label for="num_baths" class="form-label">N. Bathrooms:</label>
                            <input type="number" name="num_baths" id="num_baths" value="{{ old('num_baths') }}"
                                class="form-control @error('num_baths') is-invalid @enderror" required min ="1"
                                max="5">
                            @error('num_baths')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="mq" class="form-label">Square Meter:</label>
                            <input type="number" name="mq" id="mq" value="{{ old('mq') }}"
                                class="form-control @error('mq') is-invalid @enderror" required min="10"
                                max="5000">
                            @error('mq')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-3">
                            <label for="address" class="form-label">Address:</label>
                            <input type="address" name="address" id="address" value="{{ old('address') }}"
                                class="form-control @error('address') is-invalid @enderror" required minlength="2"
                                maxlength="100">
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-3">
                            <label for="floor" class="form-label">Floor:</label>
                            <input type="floor" name="floor" id="floor" value="{{ old('floor') }}"
                                class="form-control @error('floor') is-invalid @enderror" required minlength="2"
                                maxlength="100" required>
                            @error('floor')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label for="price" class="form-label">Price:</label>
                            <input type="number" name="price" id="price" value="{{ old('price') }}"
                                class="form-control @error('price') is-invalid @enderror" required min="10"
                                max="999999.99" required>
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label for="type" class="form-label">Type:</label>
                            <select name="type" id="type"
                                class="form-control @error('type') is-invalid @enderror" required>
                                <option value="" disabled selected>-Choose a type-</option>
                                @foreach ($propertyTypes as $type)
                                    <option value="{{ $type }}" @selected(old('type') === $type)>
                                        {{ ucfirst(str_replace('-', ' ', $type)) }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            @foreach ($sponsors as $sponsor)
                                <div class="mx-2 d-inline">
                                    <input type="checkbox" name="sponsors[]" id="sponsor_{{ $sponsor->id }}"
                                        value="{{ $sponsor->id }}" @checked(is_array(old('sponsors')) && in_array($sponsor->id, old('sponsors')))>
                                    <label for="sponsor_{{ $sponsor->id }}"
                                        class="form-label">{{ $sponsor->name }}</label>
                                </div>
                            @endforeach
                            @error('sponsors')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label for="description" class="form-label">Description:</label>
                            <textarea name="description" id="description-project" cols="30" rows="5" maxlength="300"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label for="available" class="form-label">Available</label>
                            <input type="radio" name="available" value="1" @checked(old('available') === '1') required>
                            Yes
                            <input type="radio" name="available" value="0" @checked(old('available') === '0') required>
                            No
                            @error('available')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label for="cover_image" class="form-label">Cover Image</label>
                            <input type="file" name="cover_image" id="cover_image"
                                class="form-control @error('cover_image') is-invalid @enderror">
                            @error('cover_image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-primary my-4" id="liveToastBtn">INVIA</button>
                </form>
            </div>
        </div>
    </div>
@endsection
