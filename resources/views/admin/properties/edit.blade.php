@extends('layouts.app')

@section('content')
    <div class="container my-4">
        <h1>Modifica Proprietà</h1>
        <form action="{{ route('admin.properties.update', ['property' => $property->slug]) }}" method="post"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Prima riga: Titolo e Prezzo -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="title" class="form-label">Titolo:</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $property->title) }}"
                        class="form-control @error('title') is-invalid @enderror" required maxlength="50">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="price" class="form-label">Prezzo:</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $property->price) }}"
                        class="form-control @error('price') is-invalid @enderror" min="10" max="999999.99"
                        step="0.01" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Seconda riga: Tipo -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="type" class="form-label">Tipo:</label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="" disabled>-Scegli un tipo-</option>
                        @foreach ($propertyTypes as $type)
                            <option value="{{ $type }}" @selected(old('type', $property->type) === $type)>
                                {{ ucfirst(str_replace('-', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Terza riga: Immagine di copertina -->
            <div class="mb-3">
                <label for="cover_image" class="form-label">Immagine di Copertina:</label><br>
                @if (Storage::exists('public/' . $property->cover_image))
                    <img class="img-thumbnail w-25 mb-2" src="{{ asset('storage/' . $property->cover_image) }}"
                        alt="{{ $property->name }}">
                @endif
                <input type="file" name="cover_image" id="cover_image"
                    class="form-control @error('cover_image') is-invalid @enderror">
                @error('cover_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Quarta riga: Descrizione -->
            <div class="mb-3">
                <label for="description" class="form-label">Descrizione:</label>
                <textarea name="description" id="description" rows="5"
                    class="form-control @error('description') is-invalid @enderror">{{ old('description', $property->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Quinta riga: Colonne Sinistra e Destra -->
            <div class="row mb-3">
                <!-- Colonna Sinistra -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="floor" class="form-label">Piano:</label>
                        <input type="number" name="floor" id="floor" value="{{ old('floor', $property->floor) }}"
                            class="form-control @error('floor') is-invalid @enderror" required>
                        @error('floor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="mq" class="form-label">Metri Quadri (mq):</label>
                        <input type="number" name="mq" id="mq" value="{{ old('mq', $property->mq) }}"
                            class="form-control @error('mq') is-invalid @enderror" min="10" max="5000" required
                            step="10">
                        @error('mq')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="num_rooms" class="form-label">Numero di Stanze:</label>
                        <input type="number" name="num_rooms" id="num_rooms"
                            value="{{ old('num_rooms', $property->num_rooms) }}"
                            class="form-control @error('num_rooms') is-invalid @enderror" min="1" max="50"
                            required>
                        @error('num_rooms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="num_beds" class="form-label">Numero di Letti:</label>
                        <input type="number" name="num_beds" id="num_beds"
                            value="{{ old('num_beds', $property->num_beds) }}"
                            class="form-control @error('num_beds') is-invalid @enderror" min="1" max="20"
                            required>
                        @error('num_beds')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="num_baths" class="form-label">Numero di Bagni:</label>
                        <input type="number" name="num_baths" id="num_baths"
                            value="{{ old('num_baths', $property->num_baths) }}"
                            class="form-control @error('num_baths') is-invalid @enderror" min="0" max="5"
                            required>
                        @error('num_baths')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Colonna Destra -->
                <div class="col-md-6">
                    <div class="mb-3 position-relative">
                        <label for="address" class="form-label">Indirizzo:</label>
                        <input type="text" name="address" id="address"
                            value="{{ old('address', $property->address) }}"
                            class="form-control @error('address') is-invalid @enderror" required minlength="2"
                            maxlength="100" autocomplete="off">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="suggestions" class="suggestions-list border-0"></div>
                    </div>
                    <div>
                        <label class="form-label">Mappa:</label>
                        <div id="map" style="width: 100%; height: 325px;"></div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="lat" id="lat" value="{{ old('lat', $property->lat) }}">
            <input type="hidden" name="long" id="long" value="{{ old('long', $property->long) }}">

            <!-- Checkbox dei Servizi -->
            <div class="mb-4">
                <label class="form-label">Servizi:</label>
                <div class="row">
                    @foreach ($services->chunk(ceil($services->count() / 3)) as $serviceChunk)
                        <div class="col-md-4">
                            @foreach ($serviceChunk as $service)
                                <div class="form-check d-flex align-items-center gap-3 mb-4">
                                    @if ($errors->any())
                                        <input type="checkbox" name="services[]" id="service_{{ $service->id }}"
                                            value="{{ $service->id }}" class="form-check-input"
                                            {{ in_array($service->id, old('services', [])) ? 'checked' : '' }}>
                                    @else
                                        <input type="checkbox" name="services[]" id="service_{{ $service->id }}"
                                            value="{{ $service->id }}" class="form-check-input"
                                            {{ $property->services->contains($service->id) ? 'checked' : '' }}>
                                    @endif
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
                </div>
                @error('services')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Disponibile e Pulsanti di Invio e Torna Indietro -->
            <div class="row mb-5 mt-3">
                <div class="col-md-6 d-flex align-items-center">
                    <label class="form-label d-flex align-items-center me-3">Disponibile:</label>
                    <div class="d-flex gap-3">
                        <div class="form-check d-flex align-items-center gap-2 m-0">
                            <input type="radio" name="available" id="available_yes" value="1"
                                class="form-check-input" @checked(old('available', $property->available) == '1') required>
                            <label for="available_yes" class="form-check-label m-0">Sì</label>
                        </div>
                        <div class="form-check d-flex align-items-center gap-2 m-0">
                            <input type="radio" name="available" id="available_no" value="0"
                                class="form-check-input" @checked(old('available', $property->available) == '0') required>
                            <label for="available_no" class="form-check-label m-0">No</label>
                        </div>
                    </div>
                    @error('available')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Torna Indietro</a>
                    <button type="submit" class="btn btn-primary px-4">Aggiorna</button>
                </div>
            </div>
        </form>
    </div>
@endsection
