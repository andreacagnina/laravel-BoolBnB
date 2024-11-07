@extends('layouts.app')

@section('content')
    <div class="container my-3">
        <h1>Add a New Property</h1>
        <form action="{{ route('admin.properties.store') }}" method="post" enctype="multipart/form-data"
            id="createPropertyForm">
            @csrf

            <!-- First Row: Title and Price -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="title" class="form-label">Title: *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                        class="form-control @error('title') is-invalid @enderror" required maxlength="50">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="price" class="form-label">Price: *</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">&euro;</span>
                        <input type="number" name="price" id="price" value="{{ old('price') }}"
                            class="form-control @error('price') is-invalid @enderror" min="10" max="999999.99"
                            step="0.01" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Second Row: Type -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="type" class="form-label">Type: *</label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror"
                            required>
                            <option value="" disabled selected>-Select a type-</option>
                            @foreach ($propertyTypes as $type)
                                <option value="{{ $type }}" @selected(old('type') == $type)>
                                    {{ ucfirst(str_replace('-', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Third Row: Cover Image -->
                <div class="mb-3">
                    <label for="cover_image" class="form-label">Cover Image:</label>
                    <input type="file" name="cover_image" id="cover_image"
                        class="form-control @error('cover_image') is-invalid @enderror">
                    @error('cover_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fourth Row: Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description:</label>
                    <textarea name="description" id="description" rows="5"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fifth Row: Left and Right Columns -->
                <div class="row mb-3">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="floor" class="form-label">Floor: *</label>
                            <input type="number" name="floor" id="floor" value="{{ old('floor') }}"
                                class="form-control @error('floor') is-invalid @enderror" required>
                            @error('floor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="mq" class="form-label">Square Meters (sqm): *</label>
                            <input type="number" name="mq" id="mq" value="{{ old('mq') }}"
                                class="form-control @error('mq') is-invalid @enderror" min="10" max="5000"
                                required step="10">
                            @error('mq')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="num_rooms" class="form-label">Number of Rooms: *</label>
                            <input type="number" name="num_rooms" id="num_rooms" value="{{ old('num_rooms', 1) }}"
                                class="form-control @error('num_rooms') is-invalid @enderror" min="1" max="50"
                                required>
                            @error('num_rooms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="num_beds" class="form-label">Number of Beds: *</label>
                            <input type="number" name="num_beds" id="num_beds" value="{{ old('num_beds', 1) }}"
                                class="form-control @error('num_beds') is-invalid @enderror" min="1" max="20"
                                required>
                            @error('num_beds')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="num_baths" class="form-label">Number of Bathrooms: *</label>
                            <input type="number" name="num_baths" id="num_baths" value="{{ old('num_baths', 0) }}"
                                class="form-control @error('num_baths') is-invalid @enderror" min="0"
                                max="5" required>
                            @error('num_baths')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="mb-3 position-relative">
                            <label for="address" class="form-label">Address: *</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}"
                                class="form-control @error('address') is-invalid @enderror" required minlength="2"
                                maxlength="100" autocomplete="off">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="suggestions" class="suggestions-list border-0"></div>
                        </div>
                        <div>
                            <label class="form-label">Map:</label>
                            <div id="map" style="width: 100%; height: 325px;"></div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                <input type="hidden" name="long" id="long" value="{{ old('long') }}">

                <!-- Services Checkbox -->
                <div class="mb-4">
                    <label class="form-label">Services: *</label>
                    <div class="row">
                        @foreach ($services->chunk(ceil($services->count() / 3)) as $serviceChunk)
                            <div class="col-md-4">
                                @foreach ($serviceChunk as $service)
                                    <div class="form-check d-flex align-items-center gap-3 mb-4">
                                        <input type="checkbox" name="services[]" id="service_{{ $service->id }}"
                                            value="{{ $service->id }}" class="form-check-input"
                                            @checked(is_array(old('services')) && in_array($service->id, old('services')))>
                                        <label for="service_{{ $service->id }}" class="form-check-label m-0 fw-normal">
                                            @if ($service->icon)
                                                <i class="{{ $service->icon }}"></i>
                                            @endif
                                            {{ $service->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                        <div id="error-container-services"></div>
                    </div>
                    @error('services')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Availability and Submit/Back Buttons -->
                <div class="row mb-5 mt-3">
                    <div class="col-md-6 d-flex align-items-center">
                        <label class="form-label d-flex align-items-center me-3 mb-0">Available: *</label>
                        <div class="d-flex gap-3">
                            <div class="form-check d-flex align-items-center gap-2 m-0">
                                <input type="radio" name="available" id="available_yes" value="1"
                                    class="form-check-input" @checked(old('available') === '1')>
                                <label for="available_yes" class="form-check-label m-0">Yes</label>
                            </div>
                            <div class="form-check d-flex align-items-center gap-2 m-0">
                                <input type="radio" name="available" id="available_no" value="0"
                                    class="form-check-input" @checked(old('available') === '0')>
                                <label for="available_no" class="form-check-label m-0">No</label>
                            </div>
                        </div>
                        @error('available')
                            <div class="text-danger ms-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div id="error-container-available"></div>
                    <div>(*) Required Fields</div>
                    <div class="col-md-12 text-end d-flex justify-content-end align-items-center gap-2">
                        <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Go Back</a>
                        <button type="submit" class="btn btn-primary px-4">Add</button>
                    </div>
                </div>
        </form>
    </div>
@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupPropertyValidation('createPropertyForm');
        });
    </script>
@endsection
