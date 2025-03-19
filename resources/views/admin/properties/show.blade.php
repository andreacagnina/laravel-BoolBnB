@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card border-0">
            <div class="card-header bg-cust text-white text-center">
                <h2>{{ $property->title }}</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Colonna Immagini -->
                    <div class="col-lg-6">
                        <div class="position-relative">
                            <!-- Freccia Sinistra -->
                            <button id="prev-btn" 
                                    class="btn btn-dark position-absolute top-50 start-0 translate-middle-y" 
                                    style="z-index: 10; margin-left: 15px; width: 40px; height: 40px; border-radius: 8px; padding: 8px;">
                                <i class="bi bi-arrow-left"></i>
                            </button>
                            
                            <!-- Immagine Principale con Aspect Ratio 16:9 -->
                            <div class="d-flex justify-content-center align-items-center bg-black" 
                                 style="width: 100%; height: 0; padding-top: 56.25%; position: relative; overflow: hidden; border-radius: 12px;">
                                <img id="main-image" 
                                     src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                                     class="position-absolute top-0 start-0 end-0 bottom-0 m-auto"
                                     style="max-height: 100%; max-width: 100%; object-fit: contain;"
                                     alt="{{ $property->title }}">
                            </div>

                            <!-- Freccia Destra -->
                            <button id="next-btn" 
                                    class="btn btn-dark position-absolute top-50 end-0 translate-middle-y" 
                                    style="z-index: 10; margin-right: 15px; width: 40px; height: 40px; border-radius: 8px; padding: 8px;">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>

                        <!-- Thumbnail delle immagini -->
                        <div class="d-flex justify-content-start overflow-scroll mt-3" 
                             style="gap: 10px; white-space: nowrap; overflow-y: hidden; scrollbar-width: none;">
                            <!-- Thumbnail della cover image -->
                            <img 
                                src="{{ Str::startsWith($property->cover_image, 'http') ? $property->cover_image : asset('storage/' . $property->cover_image) }}"
                                class="thumbnail-image active-thumbnail"
                                style="width: 80px; height: 80px; cursor: pointer; border-radius: 8px; object-fit: cover;"
                                alt="Thumbnail">
                            
                            <!-- Thumbnail delle altre immagini -->
                            @foreach ($property->images as $image)
                                <img 
                                    src="{{ Str::startsWith($image->path, 'http') ? $image->path : asset('storage/' . $image->path) }}"
                                    class="thumbnail-image"
                                    style="width: 80px; height: 80px; cursor: pointer; border: 2px solid transparent; border-radius: 8px; object-fit: cover;"
                                    alt="Thumbnail">
                            @endforeach
                        </div>

                        <!-- Mappa -->
                        <h4 class="my-4">Map</h4>
                        <div id="map" class="rounded mt-3" style="width: 99%; height: 300px;">
                        </div>
                        <input type="hidden" id="lat" value="{{ $property->lat }}">
                        <input type="hidden" id="long" value="{{ $property->long }}">
                    </div>

                    <!-- Colonna Informazioni -->
                    <div class="col-lg-6">
                        <h4 class="my-4">Property Information</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Description:</strong> {{ $property->description }}</li>
                            <li class="list-group-item"><strong>Address:</strong> {{ $property->address }}</li>
                            <li class="list-group-item"><strong>Floor:</strong> {{ $property->floor }}</li>
                            <li class="list-group-item"><strong>Price:</strong>
                                {{ number_format($property->price, 2, ',', '') }}&euro;</li>
                            <li class="list-group-item"><strong>Square Meters:</strong> {{ $property->mq }} sqm</li>
                            <li class="list-group-item"><strong>Number of Rooms:</strong> {{ $property->num_rooms }}</li>
                            <li class="list-group-item"><strong>Number of Beds:</strong> {{ $property->num_beds }}</li>
                            <li class="list-group-item"><strong>Number of Bathrooms:</strong> {{ $property->num_baths }}</li>
                            <li class="list-group-item"><strong>Type:</strong>
                                {{ ucfirst(str_replace('-', ' ', $property->type)) }}</li>
                            <li class="list-group-item"><strong>Available Services:</strong>
                                @if ($property->services->isEmpty())
                                    No services included.
                                @else
                                    <ul class="list-unstyled">
                                        @foreach ($property->services as $service)
                                            <li>
                                                <i class="{{ $service->icon }}"></i><span
                                                    class="ms-2">{{ $service->name }} </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                            <li class="list-group-item"><strong>Availability:</strong>
                                {{ $property->available ? 'Yes' : 'No' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Back to list</a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const thumbnails = document.querySelectorAll('.thumbnail-image');
            const mainImage = document.getElementById('main-image');
            const images = Array.from(thumbnails).map(thumbnail => thumbnail.src);

            let currentIndex = 0;

            function updateActiveThumbnail(index) {
                thumbnails.forEach(t => t.classList.remove('active-thumbnail'));
                thumbnails[index].classList.add('active-thumbnail');
                console.log(`Active thumbnail updated to index: ${index}`);
            }

            // Cambia immagine principale al clic sulla thumbnail
            thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', () => {
                    currentIndex = index;
                    mainImage.src = images[currentIndex];
                    updateActiveThumbnail(currentIndex);
                    console.log(`Thumbnail clicked: ${index}`);
                });
            });

            // Freccia "precedente"
            document.getElementById('prev-btn').addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                mainImage.src = images[currentIndex];
                updateActiveThumbnail(currentIndex);
                console.log(`Prev button clicked. New index: ${currentIndex}`);
            });

            // Freccia "successiva"
            document.getElementById('next-btn').addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % images.length;
                mainImage.src = images[currentIndex];
                updateActiveThumbnail(currentIndex);
                console.log(`Next button clicked. New index: ${currentIndex}`);
            });
        });
    </script>

    <style>
        /* Nascondi barra di scorrimento */
        .overflow-scroll::-webkit-scrollbar {
            display: none;
        }
        .overflow-scroll {
            -ms-overflow-style: none; /* IE e Edge */
            scrollbar-width: none; /* Firefox */
        }

        /* Thumbnail attiva */
        .active-thumbnail {
            border: 2px solid #007bff !important; /* Aggiunto !important */
        }

        /* Adatta immagini */
        .thumbnail-image {
            object-fit: cover;
            transition: border 0.3s ease; /* Aggiunta transizione */
        }
    </style>
@endsection
