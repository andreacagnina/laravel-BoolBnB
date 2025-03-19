@extends('layouts.app')

@section('content')
    <div class="container mb-3">
        <h1 class="text-center mb-3 mb-md-5 fw-bold">Sponsor Your Property</h1>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Dropdown per la selezione delle proprietà -->
                <div class="mb-4">
                    <label for="property-dropdown" class="form-label fw-semibold">Select a Property:</label>
                    <div class="dropdown">
                        <button 
                            class="btn btn-light dropdown-toggle w-100 text-start" 
                            type="button" 
                            id="property-dropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                            <span id="selected-property-title">-- Select a Property --</span>
                        </button>
                        <ul class="dropdown-menu w-100" 
                        aria-labelledby="property-dropdown" 
                        id="property-list" 
                        style="max-height: 300px; overflow-y: auto; background-color: #f8f9fa;">
                        @foreach ($properties as $property)
                                <li>
                                    <a 
                                        class="dropdown-item d-flex align-items-center property-option px-1" 
                                        href="#" 
                                        data-slug="{{ $property->slug }}" 
                                        data-cover="{{ $property->cover_image }}" 
                                        data-description="{{ $property->description }}">
                                        <img 
                                        src="{{ filter_var($property->cover_image, FILTER_VALIDATE_URL) ? $property->cover_image : asset('storage/' . $property->cover_image) }}" 
                                        alt="Property Cover" 
                                        class="me-1 me-md-3 rounded" 
                                        style="width: 50px; height: 50px; object-fit: cover;">                                    
                                        <span>{{ $property->title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                <!-- Informazioni sulla proprietà selezionata -->
                <div id="property-info" class="mb-3 d-none">
                    <div class="card shadow-sm">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img id="property-cover" src="" alt="Property Cover" class="img-fluid rounded-start">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold" id="property-title"></h5>
                                    <p class="card-text" id="property-description"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sponsor Cards -->
                <p class="fw-semibold mt-4">Select a Sponsor:</p>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach ($sponsors as $sponsor)
                        <div class="col">
                            <div 
                                class="card h-100 shadow-sm text-center sponsor-card" 
                                data-sponsor-id="{{ $sponsor->id }}">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold text-primary">{{ $sponsor->name }}</h5>
                                    <p class="card-text">Price: €{{ number_format($sponsor->price, 2, ',', '') }}</p>
                                    <p class="card-text">Duration: {{ $sponsor->duration }} hours</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Form per il pagamento -->
                <form action="{{ route('admin.properties.assignSponsor') }}" method="POST" id="payment-form">
                    @csrf
                    <input type="hidden" name="property_slug" id="property_slug" required>
                    <input type="hidden" name="sponsor_id" id="sponsor_id" required>
                    <input type="hidden" name="payment_method_nonce" id="payment-method-nonce">

                    <div id="dropin-container" class="mt-4 d-none"></div>

                    <div class="d-flex justify-content-end mt-4 gap-3">
                        <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Back to Properties</a>
                        <button type="button" id="pay-button" class="btn btn-primary" disabled>
                            <i class="bi bi-credit-card"></i> Pay with Braintree
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .sponsor-card {
            position: relative;
            transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s;
            cursor: pointer;
            background-color: white;
        }

        .sponsor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .sponsor-card.selected {
            background-color: rgba(255, 255, 255, 0.85); /* Bianco trasparente */
            border: 2px solid #0d6efd;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
            transform: scale(1.02);
        }

        .sponsor-card.selected::after {
            content: '\2713'; /* Icona check */
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #0d6efd;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .sponsor-card {
                font-size: 0.9rem;
            }

            #dropin-container {
                font-size: 0.7rem;
            }
        }

        @media (min-width: 1200px) {
            .sponsor-card {
                font-size: 1.1rem;
            }
        }
    </style>

    <script src="https://js.braintreegateway.com/web/dropin/1.30.0/js/dropin.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const propertyList = document.getElementById('property-list');
            const propertyInfo = document.getElementById('property-info');
            const propertySlugInput = document.getElementById('property_slug');
            const propertyCover = document.getElementById('property-cover');
            const propertyTitle = document.getElementById('property-title');
            const propertyDescription = document.getElementById('property-description');
            const selectedPropertyTitle = document.getElementById('selected-property-title');
            const sponsorCards = document.querySelectorAll('.sponsor-card');
            const sponsorIdInput = document.getElementById('sponsor_id');
            const payButton = document.getElementById('pay-button');
            const dropinContainer = document.getElementById('dropin-container');
            const paymentForm = document.getElementById('payment-form');
            let braintreeInstance = null;

            propertyList.addEventListener('click', function (e) {
                e.preventDefault();
                const target = e.target.closest('.property-option');
                if (target) {
                    const slug = target.getAttribute('data-slug');
                    const cover = target.getAttribute('data-cover');
                    const description = target.getAttribute('data-description');
                    const title = target.textContent.trim();
                    
                    // Determina l'immagine corretta (URL assoluto o percorso dello storage)
                    const coverImage = cover.startsWith('http') 
                        ? cover 
                        : `{{ asset('storage/') }}/${cover}`;

                    // Aggiorna il contenuto dinamico
                    propertySlugInput.value = slug;
                    propertyCover.src = coverImage; // Utilizza coverImage qui
                    propertyTitle.textContent = title;
                    propertyDescription.textContent = description;
                    selectedPropertyTitle.textContent = title;
                    propertyInfo.classList.remove('d-none');

                    validateForm();
                }
            });

            sponsorCards.forEach(card => {
                card.addEventListener('click', () => {
                    sponsorIdInput.value = card.getAttribute('data-sponsor-id');

                    sponsorCards.forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');

                    validateForm();
                });
            });

            function validateForm() {
                const isValid = propertySlugInput.value && sponsorIdInput.value;
                payButton.disabled = !isValid;
                dropinContainer.classList.toggle('d-none', !isValid);

                if (isValid && !braintreeInstance) {
                    initializeBraintree();
                }
            }

            function initializeBraintree() {
                fetch("{{ route('admin.braintree.token') }}")
                    .then(response => response.json())
                    .then(data => {
                        braintree.dropin.create({
                            authorization: data.token,
                            container: '#dropin-container'
                        }, (err, instance) => {
                            if (err) {
                                console.error('Braintree initialization error:', err);
                                return;
                            }
                            braintreeInstance = instance;

                            payButton.addEventListener('click', () => {
                                payButton.disabled = true;
                                payButton.textContent = 'Processing...';

                                instance.requestPaymentMethod((err, payload) => {
                                    if (err) {
                                        console.error('Payment method request error:', err);
                                        payButton.disabled = false;
                                        payButton.textContent = 'Pay with Braintree';
                                        return;
                                    }

                                    document.getElementById('payment-method-nonce').value = payload.nonce;
                                    paymentForm.submit();
                                });
                            });
                        });
                    })
                    .catch(err => {
                        console.error('Error fetching Braintree token:', err);
                    });
            }
        });
    </script>
@endsection
