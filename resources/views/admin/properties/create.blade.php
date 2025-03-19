@extends('layouts.app')

@section('content')
    <div class="container mb-3">
        <h1>Add a New Property</h1>
        <form action="{{ route('admin.properties.store') }}" method="post" enctype="multipart/form-data"
            id="createPropertyForm">
            @csrf

            <!-- First Row: Title and Price -->
            <div class="row">
                <div class="col-md-8">
                    <label for="title" class="form-label">Title: *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}"
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
                        <input type="number" name="price" id="price" value="{{ old('price') }}"
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
                        <option value="" disabled selected>-Select a type-</option>
                        @foreach ($propertyTypes as $type)
                            <option value="{{ $type }}" @selected(old('type') == $type)>
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

            <!-- Immagine di Copertina con Drag-and-Drop -->
            <div class="row">
                <div class="col-md-12">
                    <label for="cover_image" class="form-label">Immagine di Copertina:</label>
                    <div id="cover-image-drop-zone" class="drop-zone">
                        <span id="cover-drop-text">Trascina e rilascia un'immagine qui o clicca per caricare</span>
                        <input type="file" name="cover_image" id="cover_image" 
                            class="d-none @error('cover_image') is-invalid @enderror" accept="image/*">
                        <div id="cover-image-preview" class="image-preview"></div>
                    </div>
                    @error('cover_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Immagini Aggiuntive con Drag-and-Drop -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <label for="images" class="form-label">Immagini Aggiuntive:</label>
                    <div id="image-drop-zone" class="drop-zone">
                        <span id="additional-drop-text">Trascina e rilascia immagini qui o clicca per caricare</span>
                        <input type="file" name="images[]" id="images" 
                            class="d-none @error('images.*') is-invalid @enderror" multiple accept="image/*">
                        <div id="additional-images-preview" class="image-preview"></div>
                    </div>
                    @error('images.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Fifth Row: Description -->
            <div class="row">
                <div class="col-md-12">
                    <label for="description" class="form-label">Description:</label>
                    <textarea name="description" id="description" rows="5"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    <div id="error-container-description"></div>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Sixth Row: Left and Right Columns -->
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <label for="floor" class="form-label">Floor: *</label>
                    <input type="number" name="floor" id="floor" value="{{ old('floor') }}"
                        class="form-control @error('floor') is-invalid @enderror" required>
                    <div id="error-container-floor"></div>
                    @error('floor')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label for="mq" class="form-label">Square Meters (sqm): *</label>
                    <input type="number" name="mq" id="mq" value="{{ old('mq') }}"
                        class="form-control @error('mq') is-invalid @enderror" min="10" max="5000" required
                        step="10">
                    <div id="error-container-mq"></div>
                    @error('mq')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label for="num_rooms" class="form-label">Number of Rooms: *</label>
                    <input type="number" name="num_rooms" id="num_rooms" value="{{ old('num_rooms', 1) }}"
                        class="form-control @error('num_rooms') is-invalid @enderror" min="1" max="50"
                        required>
                    <div id="error-container-num_rooms"></div>
                    @error('num_rooms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label for="num_beds" class="form-label">Number of Beds: *</label>
                    <input type="number" name="num_beds" id="num_beds" value="{{ old('num_beds', 1) }}"
                        class="form-control @error('num_beds') is-invalid @enderror" min="1" max="20"
                        required>
                    <div id="error-container-num_beds"></div>
                    @error('num_beds')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label for="num_baths" class="form-label">Number of Bathrooms: *</label>
                    <input type="number" name="num_baths" id="num_baths" value="{{ old('num_baths', 0) }}"
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
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
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

            <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
            <input type="hidden" name="long" id="long" value="{{ old('long') }}">

            <!-- Services Checkbox -->
            <div class="row">
                <div class="col-12">
                    <label class="form-label">Services: *</label>
                    <div class="row">
                        @foreach ($services->chunk(ceil($services->count() / 3)) as $serviceChunk)
                            <div class="col-md-4">
                                @foreach ($serviceChunk as $service)
                                    <div class="form-check d-flex align-items-center gap-3 mb-3 cursor-pointer">
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
                </div>
                <div id="error-container-available"></div>
                @error('available')
                    <div class="text-danger ms-2">{{ $message }}</div>
                @enderror
                <div>(*) Required Fields</div>
                <div class="col-md-12 text-end d-flex justify-content-end align-items-center gap-2">
                    <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Go Back</a>
                    <button type="submit" class="btn btn-primary px-4">Add</button>
                </div>
            </div>
        </form>
    </div>

    <style>
        /* Stile per la Drop Zone */
        .drop-zone {
            border: 2px dashed #6c757d;
            border-radius: 8px;
            background-color: #192033;
            color: #fff;
            text-align: center;
            height: 120px; /* Altezza fissa della box */
            position: relative;
            overflow: hidden; /* Previene l'uscita delle immagini */
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Evidenziazione durante il Drag */
        .drop-zone.highlight {
            border-color: #007bff;
            background-color: #3a4a6b;
        }

        /* Contenitore delle Anteprime */
        .image-preview {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: wrap;
            width: 100%;
            height: 100%; /* Riempi l'altezza della box */
            padding: 5px;
            overflow-x: auto;
        }

        /* Stile per le Immagini Caricate */
        .image-preview img {
            height: 100%; /* Altezza pari alla box */
            width: auto; /* Larghezza proporzionale */
            object-fit: contain; /* Mantieni le proporzioni */
            margin-right: 5px; /* Spazio tra le immagini */
        }

        /* Nascondi il testo quando ci sono immagini */
        .drop-zone.has-images span {
            display: none;
        }

        /* Posizionamento del Testo nella Drop Zone */
        .drop-zone span {
            position: absolute;
            text-align: center;
            color: white;
            pointer-events: none;
        }
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupPropertyValidation('createPropertyForm');
        });

        document.addEventListener('DOMContentLoaded', function () {
            function handleImagePreview(inputElement, previewElement, dropZone, isSingle = false) {
                const files = inputElement.files;

                if (isSingle) {
                    // Per una singola immagine (Immagine di Copertina)
                    previewElement.innerHTML = ''; // Svuota la preview precedente
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
                    // Per più immagini (Immagini Aggiuntive)
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

            // Configura la Drop Zone per l'immagine di copertina
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

            // Configura la Drop Zone per le immagini aggiuntive
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
    </script>
@endsection
