@extends('layouts.app')

@section('content')
    <div class="container mb-3">
        <h1>Edit Property</h1>
        <form id="editPropertyForm" action="{{ route('admin.properties.update', ['property' => $property->slug]) }}"
            method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- First Row: Title and Price -->
            <div class="row">
                <div class="col-md-8">
                    <label for="title" class="form-label">Title: *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $property->title) }}"
                        class="form-control @error('title') is-invalid @enderror" required maxlength="50">
                    <div id="error-container-title"></div>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="price" class="form-label">Price: *</label>
                    <div class="input-group">
                        <span class="input-group-text">&euro;</span>
                        <input type="number" name="price" id="price" value="{{ old('price', $property->price) }}"
                            class="form-control rounded-end @error('price') is-invalid @enderror" min="10"
                            max="999999.99" step="0.01" required>
                    </div>
                    <div id="error-container-price"></div>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Second Row: Type -->
            <div class="row">
                <div class="col-md-12">
                    <label for="type" class="form-label">Type: *</label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="" disabled>-Select a type-</option>
                        @foreach ($propertyTypes as $type)
                            <option value="{{ $type }}" @selected(old('type', $property->type) === $type)>
                                {{ ucfirst(str_replace('-', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                    <div id="error-container-select"></div>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Cover Image -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <label for="cover_image" class="form-label">Cover Image:</label>
                    <div id="cover-image-drop-zone" class="drop-zone">
                        <div id="cover-image-preview" class="image-preview position-relative">
                            @if ($property->cover_image)
                                <div class="position-relative">
                                    <img src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}" 
                                        alt="Cover Image">
                                        <button type="button" class="delete-btn" onclick="removeCoverImage(event)">&times;</button>                                  
                                </div>
                            @endif
                        </div>
                        <input type="file" name="cover_image" id="cover_image" class="d-none">
                    </div>
                </div>
            </div>

            <!-- Additional Images -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <label for="images" class="form-label">Additional Images:</label>
                    <div id="image-drop-zone" class="drop-zone">
                        <div id="additional-images-preview" class="image-preview">
                            @foreach ($property->images as $image)
                                <div class="position-relative">
                                    <img src="{{ Str::startsWith($image->path, 'http') ? $image->path : asset('storage/' . $image->path) }}" 
                                        alt="Additional Image">
                                        <button type="button" class="delete-btn" onclick="removeImage('{{ $image->id }}', event)">&times;</button>                                  
                                </div>
                            @endforeach
                        </div>
                        <input type="file" name="images[]" id="images" class="d-none" multiple>
                    </div>
                </div>
            </div>

            <!-- Fourth Row: Description -->
            <div class="row">
                <div class="col-md-12">
                    <label for="description" class="form-label">Description:</label>
                    <textarea name="description" id="description" rows="5"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description', $property->description) }}</textarea>
                    <div id="error-container-description"></div>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Fifth Row: Left and Right Columns -->
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <label for="floor" class="form-label">Floor: *</label>
                    <input type="number" name="floor" id="floor" value="{{ old('floor', $property->floor) }}"
                        class="form-control @error('floor') is-invalid @enderror" required>
                    <div id="error-container-floor"></div>
                    @error('floor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label for="mq" class="form-label">Square Meters (sqm): *</label>
                    <input type="number" name="mq" id="mq" value="{{ old('mq', $property->mq) }}"
                        class="form-control @error('mq') is-invalid @enderror" min="10" max="5000" required
                        step="10">
                    <div id="error-container-mq"></div>
                    @error('mq')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label for="num_rooms" class="form-label">Number of Rooms: *</label>
                    <input type="number" name="num_rooms" id="num_rooms"
                        value="{{ old('num_rooms', $property->num_rooms) }}"
                        class="form-control @error('num_rooms') is-invalid @enderror" min="1" max="50"
                        required>
                    <div id="error-container-num_rooms"></div>
                    @error('num_rooms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label for="num_beds" class="form-label">Number of Beds: *</label>
                    <input type="number" name="num_beds" id="num_beds"
                        value="{{ old('num_beds', $property->num_beds) }}"
                        class="form-control @error('num_beds') is-invalid @enderror" min="1" max="20"
                        required>
                    <div id="error-container-num_beds"></div>
                    @error('num_beds')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label for="num_baths" class="form-label">Number of Bathrooms: *</label>
                    <input type="number" name="num_baths" id="num_baths"
                        value="{{ old('num_baths', $property->num_baths) }}"
                        class="form-control @error('num_baths') is-invalid @enderror" min="0" max="5"
                        required>
                    <div id="error-container-num_baths"></div>
                    @error('num_baths')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="position-relative">
                        <label for="address" class="form-label">Address: *</label>
                        <input type="text" name="address" id="address"
                            value="{{ old('address', $property->address) }}"
                            class="form-control @error('address') is-invalid @enderror" required minlength="2"
                            maxlength="100" autocomplete="off">
                        <div id="error-container-address"></div>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="suggestions-create-edit" class="list-group position-absolute w-100 shadow-sm"
                            style="z-index: 1000;"></div>
                    </div>

                    <label class="form-label">Map:</label>
                    <div id="map" style="width: 100%; height: 262px;"></div>
                </div>
            </div>

            <input type="hidden" name="lat" id="lat" value="{{ old('lat', $property->lat) }}">
            <input type="hidden" name="long" id="long" value="{{ old('long', $property->long) }}">

            <!-- Services Checkbox -->
            <div class="row">
                <div class="col-12">
                    <label class="form-label">Services:</label>
                    <div class="row">
                        @foreach ($services->chunk(ceil($services->count() / 24)) as $serviceChunk)
                            <div class="col-6 col-md-4">
                                @foreach ($serviceChunk as $service)
                                    <div class="form-check d-flex align-items-center gap-3 mb-3 cursor-pointer">
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
                    <div id="error-container-services"></div>
                    @error('services')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Availability and Submit/Back Buttons -->
            <div class="row">
                <div class="col-md-6 d-flex align-items-center cursor-pointer">
                    <label class="form-label d-flex align-items-center me-3 mb-0">Available:</label>
                    <div class="d-flex gap-3">
                        <div class="form-check d-flex align-items-center gap-2 m-0">
                            <input type="radio" name="available" id="available_yes" value="1"
                                class="form-check-input" @checked(old('available', $property->available) == '1') required>
                            <label for="available_yes" class="form-check-label m-0">Yes</label>
                        </div>
                        <div class="form-check d-flex align-items-center gap-2 m-0">
                            <input type="radio" name="available" id="available_no" value="0"
                                class="form-check-input" @checked(old('available', $property->available) == '0') required>
                            <label for="available_no" class="form-check-label m-0">No</label>
                        </div>
                    </div>
                    <div id="error-container-available"></div>
                    @error('available')
                        <div class="text-danger ms-2">{{ $message }}</div>
                    @enderror
                </div>
                <div>(*) Required Fields</div>
                <div class="col-md-12 text-end d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Go Back</a>
                    <button type="submit" class="btn btn-primary px-4">Update</button>
                </div>
            </div>
        </form>
    </div>

    <style>
        .drop-zone {
            border: 2px dashed #6c757d;
            border-radius: 8px;
            background-color: #192033;
            color: #fff;
            text-align: start;
            min-height: 120px;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: start;
            padding-left: 10px;
        }

        .drop-zone img {
            max-height: 100px;
            border-radius: 8px;
        }

        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: start;
        }

        .drop-zone input[type='file'] {
            display: none;
        }

        .drop-zone.highlight {
            border-color: #007bff;
            background-color: #3a4a6b;
        }

        .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            z-index: 10; /* Portalo in primo piano */
            font-size: 12px; /* Riduci la dimensione del font */
            width: 20px;
            height: 20px;
            padding: 0; /* Rimuovi il padding interno */
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%; /* Rendi il bottone rotondo */
            background-color: #dc3545; /* Rosso per indicare eliminazione */
            color: #fff; /* Bianco per il testo */
            border: none;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #c82333; /* Colore più scuro al passaggio del mouse */
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Helper: Manage image preview
            function handleImagePreview(inputElement, previewElement, dropZone, isSingle = false) {
                const files = inputElement.files;

                if (isSingle) {
                    previewElement.innerHTML = '';
                    if (files.length > 0) {
                        const file = files[0];
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                previewElement.appendChild(img);
                            };
                            reader.readAsDataURL(file);
                            dropZone.classList.add('has-images');
                        }
                    }
                } else {
                    Array.from(files).forEach(file => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                previewElement.appendChild(img);
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                    dropZone.classList.add('has-images');
                }
            }

            function mergeFiles(existingInput, newFiles) {
                const dataTransfer = new DataTransfer();
                Array.from(existingInput.files).forEach(file => dataTransfer.items.add(file));
                Array.from(newFiles).forEach(file => dataTransfer.items.add(file));
                existingInput.files = dataTransfer.files;
            }

            // Cover Image Logic
            const coverImageInput = document.getElementById('cover_image');
            const coverImageDropZone = document.getElementById('cover-image-drop-zone');
            const coverImagePreview = document.getElementById('cover-image-preview');

            coverImageInput.addEventListener('change', function () {
                handleImagePreview(coverImageInput, coverImagePreview, coverImageDropZone, true);
            });

            coverImageDropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                coverImageDropZone.classList.add('highlight');
            });

            coverImageDropZone.addEventListener('dragleave', function () {
                coverImageDropZone.classList.remove('highlight');
            });

            coverImageDropZone.addEventListener('drop', function (e) {
                e.preventDefault();
                coverImageDropZone.classList.remove('highlight');
                coverImageInput.files = e.dataTransfer.files;
                handleImagePreview(coverImageInput, coverImagePreview, coverImageDropZone, true);
            });

            coverImageDropZone.addEventListener('click', function () {
                coverImageInput.click();
            });

            // Additional Images Logic
            const additionalImagesInput = document.getElementById('images');
            const additionalImageDropZone = document.getElementById('image-drop-zone');
            const additionalImagePreview = document.getElementById('additional-images-preview');

            additionalImagesInput.addEventListener('change', function () {
                handleImagePreview(additionalImagesInput, additionalImagePreview, additionalImageDropZone, false);
            });

            additionalImageDropZone.addEventListener('dragover', function (e) {
                e.preventDefault();
                additionalImageDropZone.classList.add('highlight');
            });

            additionalImageDropZone.addEventListener('dragleave', function () {
                additionalImageDropZone.classList.remove('highlight');
            });

            additionalImageDropZone.addEventListener('drop', function (e) {
                e.preventDefault();
                additionalImageDropZone.classList.remove('highlight');
                mergeFiles(additionalImagesInput, e.dataTransfer.files);
                handleImagePreview(additionalImagesInput, additionalImagePreview, additionalImageDropZone, false);
            });

            additionalImageDropZone.addEventListener('click', function () {
                additionalImagesInput.click();
            });
        });

        function removeImage(imageId, event) {
            // Impedisci il comportamento di default e il bubbling dell'evento
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Trova l'elemento immagine da rimuovere
            const imageElement = document.querySelector(`button[onclick="removeImage('${imageId}', event)"]`).parentElement;
            imageElement.remove();

            // Gestisci il campo nascosto per le immagini eliminate
            let deleteInput = document.getElementById('deleted_images');
            if (!deleteInput) {
                deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'deleted_images';
                deleteInput.id = 'deleted_images';
                document.getElementById('editPropertyForm').appendChild(deleteInput);
            }

            const currentValues = deleteInput.value ? deleteInput.value.split(',') : [];
            if (!currentValues.includes(imageId.toString())) {
                currentValues.push(imageId.toString());
            }
            deleteInput.value = currentValues.join(',');
        }

        function removeCoverImage(event) {
            // Impedisci il comportamento di default e il bubbling dell'evento
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Rimuovi l'anteprima dell'immagine di copertina
            const coverImagePreview = document.getElementById('cover-image-preview');
            coverImagePreview.innerHTML = ''; // Rimuove l'immagine dalla visualizzazione

            // Aggiungi un campo nascosto per segnalare la rimozione dell'immagine di copertina
            let deleteCoverInput = document.getElementById('deleted_cover_image');
            if (!deleteCoverInput) {
                deleteCoverInput = document.createElement('input');
                deleteCoverInput.type = 'hidden';
                deleteCoverInput.name = 'deleted_cover_image';
                deleteCoverInput.id = 'deleted_cover_image';
                document.getElementById('editPropertyForm').appendChild(deleteCoverInput);
            }
            deleteCoverInput.value = 'true';
        }
    </script>
@endsection
