@extends('layouts.app')

@section('content')
    <div class="container my-3">
        <div class="row">
            <div class="col-12">
                <div class="content">
                    <h1>Modifica Property</h1>
                </div>
            </div>
            <div class="col-12">
                <form action="{{ route('admin.properties.update', $property->id) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="col-12">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $property->title) }}"
                            class="form-control @error('title') is-invalid @enderror" required maxlength="50">
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="type" class="form-label">Type:</label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror"
                            required>
                            <option value="" disabled>-Choose a type-</option>
                            @foreach ($propertyTypes as $type)
                                <option value="{{ $type }}" @selected(old('type', $property->type) === $type)>
                                    {{ ucfirst(str_replace('-', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-7 position-relative">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" name="address" id="address" value="{{ old('address', $property->address) }}"
                            class="form-control @error('address') is-invalid @enderror" required minlength="2"
                            maxlength="100" autocomplete="off">
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div id="suggestions" class="suggestions-list border-0"></div>
                    </div>

                    <div class="col-12">
                        <label for="cover_image" class="form-label">Cover Image:</label><br>
                        <img class="img-thumbnail w-25 mb-2" src="{{ asset('storage/' . $property->cover_image) }}"
                            alt="{{ $property->name }}">
                        <input type="file" name="cover_image" id="cover_image"
                            class="form-control @error('cover_image') is-invalid @enderror">
                        @error('cover_image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Description:</label>
                        <textarea name="description" id="description" rows="5"
                            class="form-control @error('description') is-invalid @enderror">{{ old('description', $property->description) }}</textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-2">
                        <label for="mq" class="form-label">Square Meters (mq):</label>
                        <input type="number" name="mq" id="mq" value="{{ old('mq', $property->mq) }}"
                            class="form-control @error('mq') is-invalid @enderror" min="10" max="5000" required>
                        @error('mq')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-2">
                            <label for="num_rooms" class="form-label">Number of Rooms:</label>
                            <input type="number" name="num_rooms" id="num_rooms"
                                value="{{ old('num_rooms', $property->num_rooms) }}"
                                class="form-control @error('num_rooms') is-invalid @enderror" min="1" max="50"
                                required>
                            @error('num_rooms')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-2">
                            <label for="num_beds" class="form-label">Number of Beds:</label>
                            <input type="number" name="num_beds" id="num_beds"
                                value="{{ old('num_beds', $property->num_beds) }}"
                                class="form-control @error('num_beds') is-invalid @enderror" min="1" max="20"
                                required>
                            @error('num_beds')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-2">
                            <label for="num_baths" class="form-label">Number of Bathrooms:</label>
                            <input type="number" name="num_baths" id="num_baths"
                                value="{{ old('num_baths', $property->num_baths) }}"
                                class="form-control @error('num_baths') is-invalid @enderror" min="0"
                                max="5" required>
                            @error('num_baths')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-2">
                        <label for="floor" class="form-label">Floor:</label>
                        <input type="number" name="floor" id="floor"
                            value="{{ old('floor', $property->floor) }}"
                            class="form-control @error('floor') is-invalid @enderror" required>
                        @error('floor')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <input type="hidden" name="lat" id="lat" value="{{ old('lat', $property->lat) }}">
                    <input type="hidden" name="long" id="long" value="{{ old('long', $property->long) }}">

                    <div class="col-2">
                        <label for="price" class="form-label">Price:</label>
                        <input type="number" name="price" id="price"
                            value="{{ old('price', $property->price) }}"
                            class="form-control @error('price') is-invalid @enderror" min="10" max="999999.99"
                            step="0.01" required>
                        @error('price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="available" class="form-label">Available:</label>
                        <input type="radio" name="available" value="1" @checked(old('available', $property->available) == '1') required> Yes
                        <input type="radio" name="available" value="0" @checked(old('available', $property->available) == '0') required> No
                        @error('available')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-1">
                        <button type="submit" class="btn btn-primary mt-2">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
