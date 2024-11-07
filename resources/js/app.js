import './bootstrap';
import '~resources/scss/app.scss';
import '~icons/bootstrap-icons.scss';
import JustValidate from 'just-validate';
import * as bootstrap from 'bootstrap';
import.meta.glob(['../img/**']);

// Chiave API TomTom
const TOMTOM_API_KEY = 'N4TIi8FzWNZv1sUqEUsREdKHYaG6HhSU';

// Modale di conferma per la delete
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

// Messaggio di conferma
document.addEventListener('DOMContentLoaded', function () {
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.display = 'none';
        }, 3000);
    }
});

// Inizializzazione della mappa per la modifica dell'indirizzo
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
        if (map && marker) {
            map.setCenter([longitude, latitude]);
            map.setZoom(zoomLevel);
            marker.setLngLat([longitude, latitude]);
        }
    }

    // Funzionalità di suggerimenti indirizzo e aggiornamento mappa nella pagina di modifica
    const addressInput = document.getElementById('address');
    const suggestionsList = document.getElementById('suggestions');
    let suggestionsData = [];
    let selectedAddress = null;
    let activeSuggestionIndex = -1;

    // Funzione per ottenere suggerimenti per l'indirizzo
    function fetchAddressSuggestions(query) {
        fetch(`https://api.tomtom.com/search/2/search/${encodeURIComponent(query)}.json?key=${TOMTOM_API_KEY}&countrySet=IT&typeahead=true&limit=5`)
            .then(response => response.json())
            .then(data => {
                suggestionsData = data.results;
                displayAddressSuggestions();
            })
            .catch(error => console.error('Error fetching address suggestions:', error));
    }

    // Visualizzare i suggerimenti per l'indirizzo
    function displayAddressSuggestions() {
        suggestionsList.innerHTML = '';
        suggestionsData.forEach((result, index) => {
            const suggestionItem = document.createElement('a');
            suggestionItem.classList.add('list-group-item', 'list-group-item-action'); // Stile della homepage
            suggestionItem.textContent = result.address.freeformAddress;
            suggestionItem.addEventListener('click', () => selectAddressSuggestion(index));
            suggestionsList.appendChild(suggestionItem);
        });
        suggestionsList.style.display = suggestionsData.length ? 'block' : 'none';
    }    

    // Selezionare un suggerimento dall'elenco
    function selectAddressSuggestion(index) {
        const result = suggestionsData[index];
        selectedAddress = result;
        addressInput.value = result.address.freeformAddress;
        latInput.value = result.position.lat;
        longInput.value = result.position.lon;
        updateMap(result.position.lat, result.position.lon);
        suggestionsList.innerHTML = '';
        suggestionsList.style.display = 'none';
    }

    // Eventi per l'input di indirizzo
    addressInput.addEventListener('input', function () {
        const query = addressInput.value.trim();
        selectedAddress = null;
        activeSuggestionIndex = -1;
        if (query.length > 1) {
            fetchAddressSuggestions(query);
        } else {
            suggestionsList.innerHTML = '';
            suggestionsList.style.display = 'none';
        }
    });

    addressInput.addEventListener('keydown', function (event) {
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
                selectAddressSuggestion(activeSuggestionIndex);
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

    // Convalida del form di modifica per assicurarsi che lat e long siano impostati
    document.getElementById('editPropertyForm').addEventListener('submit', function (event) {
        if (!latInput.value || !longInput.value) {
            event.preventDefault();
            alert('Seleziona un indirizzo valido dai suggerimenti.');
        }
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
