@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="content p-4 border rounded shadow-sm bg-light">
                    <h3 class="mb-4 text-center">Select a Sponsor and a Property</h3>

                    <!-- Form to select sponsor and property -->
                    <form action="{{ route('admin.properties.assignSponsor') }}" method="POST" id="payment-form">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="property_slug" class="form-label">Select a Property:</label>
                            <select name="property_slug" id="property_slug" class="form-select" required>
                                <option value="" disabled selected>-- Select a Property --</option>
                                @foreach ($properties->sortBy('title') as $property)
                                    <option value="{{ $property->slug }}">
                                        {{ $property->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="sponsor_id" class="form-label">Select a Sponsor:</label>
                            <select name="sponsor_id" id="sponsor_id" class="form-select" required>
                                <option value="" disabled selected>-- Select a Sponsor --</option>
                                @foreach ($sponsors as $sponsor)
                                    <option value="{{ $sponsor->id }}" data-price="{{ $sponsor->price }}">
                                        {{ $sponsor->name }} - €{{ number_format($sponsor->price, 2, ',', '') }} for
                                        {{ $sponsor->duration }} hours
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="payment_method_nonce" id="payment-method-nonce">

                        <div id="dropin-container" class="d-none"></div>

                        <div class="text-end d-flex justify-content-end align-items-center gap-2">
                            <a href="{{ route('admin.properties.index') }}" class="btn btn-secondary">Go Back</a>
                            <button type="button" id="pay-button" class="btn btn-primary px-4" disabled>Pay with
                                Braintree</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.braintreegateway.com/web/dropin/1.30.0/js/dropin.min.js"></script>
    <script>
        const sponsorSelect = document.getElementById('sponsor_id');
        const propertySelect = document.getElementById('property_slug');
        const payButton = document.getElementById('pay-button');
        const sponsorIdInput = document.getElementById('sponsor_id');
        const container = document.getElementById('dropin-container');

        // Disabilita il pulsante di pagamento fino alla selezione di un'opzione valida
        payButton.disabled = true;

        // Abilita il pulsante solo quando viene selezionata un'opzione valida
        function validateForm() {
            payButton.disabled = !(sponsorSelect.value && propertySelect.value);
            container.classList.toggle('d-none', !sponsorSelect.value || !propertySelect.value);
        }

        sponsorSelect.addEventListener('change', validateForm);
        propertySelect.addEventListener('change', validateForm);

        // Fetch del token dal server
        fetch("{{ route('admin.braintree.token') }}")
            .then(response => response.json())
            .then(data => {
                braintree.dropin.create({
                    authorization: data.token,
                    container: '#dropin-container'
                }, function(createErr, instance) {
                    if (createErr) {
                        console.error('Error creating Braintree Drop-in:', createErr);
                        return;
                    }

                    payButton.addEventListener('click', () => {
                        instance.requestPaymentMethod((err, payload) => {
                            if (err) {
                                console.error('Error requesting payment method:', err);
                                return;
                            }

                            // Imposta il nonce nel campo nascosto del form
                            document.getElementById('payment-method-nonce').value = payload
                                .nonce;

                            // Invia il form
                            document.getElementById('payment-form').submit();
                        });
                    });
                });
            })
            .catch(error => console.error('Error fetching Braintree token:', error));
    </script>
@endsection
