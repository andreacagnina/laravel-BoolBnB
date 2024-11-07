import './bootstrap';
import '~resources/scss/app.scss';
import '~icons/bootstrap-icons.scss';
import JustValidate from 'just-validate';
import * as bootstrap from 'bootstrap';
import.meta.glob(['../img/**']);

// Chiave API TomTom
const TOMTOM_API_KEY = 'N4TIi8FzWNZv1sUqEUsREdKHYaG6HhSU';

// MODALE DELLA DELETE
const delete_buttons = document.querySelectorAll('.delete');
delete_buttons.forEach((button) => {
    button.addEventListener('click', (event) => {
        event.preventDefault();
        const modal = document.getElementById('deleteModal');
        const bootstrap_modal = new bootstrap.Modal(modal);
        bootstrap_modal.show();

        const buttonDelete = modal.querySelector('.confirm-delete');
        const propertyName = button.getAttribute('data-propertyName');
        const ModalText = modal.querySelector('#modal_text');
        ModalText.innerHTML = `Sei sicuro di voler cancellare questo immobile: <strong>${propertyName}</strong> ?`;

        buttonDelete.addEventListener('click', function () {
            button.parentElement.submit();
        });
    });
});

// MESSAGGIO DI CONFERMA
document.addEventListener('DOMContentLoaded', function () {
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.display = 'none';
        }, 3000);
    }
});

// Inizializzazione della mappa
document.addEventListener('DOMContentLoaded', function () {
    const mapContainer = document.getElementById('map');
    const latInput = document.getElementById('lat');
    const longInput = document.getElementById('long');

    let map, marker;
    if (mapContainer && latInput && longInput) {
        const lat = parseFloat(latInput.value) || 41.8719;
        const long = parseFloat(longInput.value) || 12.5674;

        map = tt.map({
            key: TOMTOM_API_KEY,
            container: 'map',
            center: [long, lat],
            zoom: latInput.value && longInput.value ? 15 : 4
        });
        marker = new tt.Marker().setLngLat([long, lat]).addTo(map);
    }
    function updateMap(latitude, longitude, zoomLevel = 15) {
        map.setCenter([longitude, latitude]);
        map.setZoom(zoomLevel);
        marker.setLngLat([longitude, latitude]);
    }
});

// Funzioni di filtro e ricerca
document.addEventListener('DOMContentLoaded', function () {
    const citySearch = document.getElementById('citySearch');
    const suggestionsList = document.getElementById('suggestions');
    const resultsContainer = document.getElementById('resultsContainer');
    const radiusInput = document.getElementById('radius');
    const roomsInput = document.getElementById('rooms');
    const bedsInput = document.getElementById('beds');
    const searchButton = document.getElementById('searchButton');
    const applyFiltersButton = document.getElementById('applyFiltersButton');
    const resetFiltersButton = document.getElementById('resetFiltersButton');
    const filterModalElement = document.getElementById('filterModal');
    const filterModal = new bootstrap.Modal(filterModalElement);

    let suggestionsData = [];
    let selectedAddress = null;
    let activeSuggestionIndex = -1;

    // Reset dei filtri
    resetFiltersButton.addEventListener('click', function () {
        roomsInput.value = '1';
        bedsInput.value = '1';
        radiusInput.value = '20';
        document.querySelectorAll('input[name="services[]"]').forEach(checkbox => checkbox.checked = false);
        citySearch.value = '';
        selectedAddress = null;
        fetchProperties();
        filterModal.hide();
    });

    // Funzione per ottenere suggerimenti dall'API TomTom
    function fetchSuggestions(query) {
        fetch(`https://api.tomtom.com/search/2/search/${encodeURIComponent(query)}.json?key=${TOMTOM_API_KEY}&countrySet=IT&typeahead=true&limit=5&entityType=Municipality`)
            .then(response => response.json())
            .then(data => {
                suggestionsData = data.results;
                displaySuggestions();
            })
            .catch(error => console.error('Error fetching suggestions:', error));
    }

    function displaySuggestions() {
        suggestionsList.innerHTML = '';
        suggestionsData.forEach((result, index) => {
            const suggestionItem = document.createElement('a');
            suggestionItem.classList.add('list-group-item', 'list-group-item-action');
            suggestionItem.textContent = result.address.freeformAddress;
            suggestionItem.addEventListener('click', () => selectSuggestion(index));
            suggestionsList.appendChild(suggestionItem);
        });
        suggestionsList.style.display = suggestionsData.length ? 'block' : 'none';
    }

    function selectSuggestion(index) {
        const result = suggestionsData[index];
        selectedAddress = result;
        citySearch.value = result.address.freeformAddress;
        suggestionsList.innerHTML = '';
        suggestionsList.style.display = 'none';
    }

    citySearch.addEventListener('input', function () {
        const query = citySearch.value.trim();
        selectedAddress = null;
        activeSuggestionIndex = -1;
        if (query.length > 1) {
            fetchSuggestions(query);
        } else {
            suggestionsList.innerHTML = '';
            suggestionsList.style.display = 'none';
        }
    });

    citySearch.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowDown') {
            if (activeSuggestionIndex < suggestionsData.length - 1) {
                activeSuggestionIndex++;
                updateActiveSuggestion();
            }
        } else if (event.key === 'ArrowUp') {
            if (activeSuggestionIndex > 0) {
                activeSuggestionIndex--;
                updateActiveSuggestion();
            }
        } else if (event.key === 'Enter') {
            event.preventDefault();
            if (activeSuggestionIndex >= 0) {
                selectSuggestion(activeSuggestionIndex);
            } else {
                selectedAddress = null;
            }
            suggestionsList.style.display = 'none';
        }
    });

    function updateActiveSuggestion() {
        const items = suggestionsList.querySelectorAll('.list-group-item');
        items.forEach((item, index) => item.classList.toggle('active', index === activeSuggestionIndex));
    }

    function fetchProperties(latitude = null, longitude = null) {
        const radius = radiusInput.value || '20';
        const rooms = roomsInput.value || '1';
        const beds = bedsInput.value || '1';
        const selectedServices = Array.from(document.querySelectorAll('input[name="services[]"]:checked')).map(checkbox => checkbox.value);

        let url = `/properties`;
        const params = new URLSearchParams();
        if (rooms !== '1') params.append('rooms', rooms);
        if (beds !== '1') params.append('beds', beds);
        if (radius !== '20') params.append('radius', radius);
        if (latitude && longitude) {
            params.append('latitude', latitude);
            params.append('longitude', longitude);
        }

        selectedServices.forEach(serviceId => params.append('services[]', serviceId));
        url += `?${params.toString()}`;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.json())
            .then(data => updateResults(data.properties))
            .catch(error => console.error('Error in AJAX request:', error));
    }

    function updateResults(properties) {
        resultsContainer.innerHTML = properties.length
            ? properties.map(property => `
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm ${property.sponsored ? 'border-success' : ''}">
                        ${property.sponsored ? '<span class="badge bg-success position-absolute top-0 end-0 m-2">Sponsored</span>' : ''}
                        <div class="overflow-hidden" style="height: 200px;">
                            <img src="${property.cover_image_url}" class="card-img-top w-100 h-100" style="object-fit: cover;" alt="${property.title}">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${property.title}</h5>
                            <p class="card-text text-muted">${property.description.substring(0, 60)}...</p>
                            <p class="card-text"><strong>Price:</strong> ${parseFloat(property.price).toFixed(2)}&euro;</p>
                            <p class="card-text"><strong>Location:</strong> ${property.address}</p>
                            ${property.distance ? `<p class="card-text"><strong>Distance:</strong> ${property.distance} km</p>` : ''}
                            <a href="/properties/${property.slug}" class="mt-auto btn btn-outline-primary">View Details</a>
                        </div>
                    </div>
                </div>`).join('')
            : '<p>No properties found within the specified criteria.</p>';
    }

    searchButton.addEventListener('click', () => fetchProperties(selectedAddress?.position.lat, selectedAddress?.position.lon));
    applyFiltersButton.addEventListener('click', () => {
        fetchProperties();
        filterModal.hide();
    });
});

// Validazione dei form con JustValidate
document.addEventListener('DOMContentLoaded', function () {
    function trimFormFields(form, fields) {
        form.addEventListener('submit', function () {
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) field.value = field.value.trim();
            });
        });
    }

    // Validazione form di login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        trimFormFields(loginForm, ['email', 'password']);
        const validation = new JustValidate('#loginForm');
        validation
            .addField('#email', [{ rule: 'required', errorMessage: 'Enter a valid email address' }, { rule: 'email', errorMessage: 'Enter a valid email address' }])
            .addField('#password', [{ rule: 'required', errorMessage: 'The password is required' }, { rule: 'minLength', value: 8, errorMessage: 'The password must contain at least 8 characters' }])
            .onSuccess(() => loginForm.submit());
    }

    // Validazione form di registrazione
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        trimFormFields(registerForm, ['email', 'password', 'password-confirm']);
        const validation = new JustValidate('#registerForm');
        validation
            .addField('#name', [{ rule: 'maxLength', value: 50, errorMessage: 'The name must not exceed 50 characters' }])
            .addField('#surname', [{ rule: 'maxLength', value: 50, errorMessage: 'The surname must not exceed 50 characters' }])
            .addField('#birth_date', [{ rule: 'customRegexp', value: /^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])-\d{4}$/, errorMessage: 'Enter a valid date in DD-MM-YYYY format' }])
            .addField('#email', [{ rule: 'required', errorMessage: 'The email address is required' }, { rule: 'email', errorMessage: 'Enter a valid email address' }])
            .addField('#password', [{ rule: 'required', errorMessage: 'The password is required' }, { rule: 'minLength', value: 8, errorMessage: 'The password must contain at least 8 characters' }, { rule: 'maxLength', value: 255, errorMessage: 'The password must not exceed 255 characters' }])
            .addField('#password-confirm', [{ rule: 'required', errorMessage: 'Password confirmation is required' }, { validator: value => value === document.getElementById('password').value, errorMessage: 'The passwords do not match' }])
            .onSuccess(() => registerForm.submit());
    }

    // Validazione form di creazione e modifica proprietà
    ['createPropertyForm', 'editPropertyForm'].forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            trimFormFields(form, ['title', 'address', 'description']);
            const validation = new JustValidate(form);
            validation
                .addField('#title', [{ rule: 'required', errorMessage: 'The title is required' }, { rule: 'maxLength', value: 50, errorMessage: 'The title must not exceed 50 characters' }])
                .addField('#cover_image', [{ rule: 'maxFilesCount', value: 1, errorMessage: 'Only one file is allowed' }, { rule: 'files', value: { files: { extensions: ['jpg', 'jpeg', 'png', 'gif'], maxSize: 4096 * 1024 } }, errorMessage: 'The file must be an image and not exceed 4MB' }])
                .addField('#description', [{ rule: 'maxLength', value: 300, errorMessage: 'The description must not exceed 300 characters' }])
                .addField('#num_rooms', [{ rule: 'required', errorMessage: 'The number of rooms is required' }, { rule: 'number', errorMessage: 'The number of rooms must be a valid number' }, { rule: 'minNumber', value: 1, errorMessage: 'The number of rooms must be at least 1' }, { rule: 'maxNumber', value: 50, errorMessage: 'The number of rooms must not exceed 50' }])
                .addField('#num_beds', [{ rule: 'required', errorMessage: 'The number of beds is required' }, { rule: 'number', errorMessage: 'The number of beds must be a valid number' }, { rule: 'minNumber', value: 1, errorMessage: 'The number of beds must be at least 1' }, { rule: 'maxNumber', value: 20, errorMessage: 'The number of beds must not exceed 20' }])
                .addField('#num_baths', [{ rule: 'required', errorMessage: 'The number of bathrooms is required' }, { rule: 'number', errorMessage: 'The number of bathrooms must be a valid number' }, { rule: 'minNumber', value: 0, errorMessage: 'The number of bathrooms must be at least 0' }, { rule: 'maxNumber', value: 5, errorMessage: 'The number of bathrooms must not exceed 5' }])
                .addField('#mq', [{ rule: 'required', errorMessage: 'The square meters are required' }, { rule: 'number', errorMessage: 'The square meters must be a valid number' }, { rule: 'minNumber', value: 10, errorMessage: 'The square meters must be at least 10' }, { rule: 'maxNumber', value: 5000, errorMessage: 'The square meters must not exceed 5000' }])
                .addField('#address', [{ rule: 'required', errorMessage: 'The address is required' }, { rule: 'minLength', value: 2, errorMessage: 'The address must be at least 2 characters' }, { rule: 'maxLength', value: 100, errorMessage: 'The address must not exceed 100 characters' }])
                .addField('#price', [{ rule: 'required', errorMessage: 'The price is required' }, { rule: 'number', errorMessage: 'The price must be a valid number' }, { rule: 'minNumber', value: 10, errorMessage: 'The price must be at least 10' }, { rule: 'maxNumber', value: 999999.99, errorMessage: 'The price must not exceed 999999.99' }])
                .addField('#type', [{ rule: 'required', errorMessage: 'The type is required' }])
                .addField('#floor', [{ rule: 'required', errorMessage: 'The floor is required' }, { rule: 'integer', errorMessage: 'The floor must be an integer' }])
                .addField('[name="services[]"]', [{ validator: () => Array.from(document.querySelectorAll('[name="services[]"]')).some(checkbox => checkbox.checked), errorMessage: 'Please select at least one service' }], { errorsContainer: document.getElementById('error-container-services') })
                .addField('[name="available"]', [{ validator: () => Array.from(document.querySelectorAll('[name="available"]')).some(radio => radio.checked), errorMessage: 'Please select availability' }], { errorsContainer: document.getElementById('error-container-available') })
                .onSuccess(() => form.submit());
        }
    });
});
