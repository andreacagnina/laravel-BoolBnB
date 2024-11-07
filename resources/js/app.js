import './bootstrap';
import '~resources/scss/app.scss';
import '~icons/bootstrap-icons.scss';
import JustValidate from 'just-validate';
import * as bootstrap from 'bootstrap';
import.meta.glob([
    '../img/**'
])

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
        ModalText.innerHTML = `Are you sure you want to delete this property?: <strong>${propertyName}</strong> ?`;

        buttonDelete.addEventListener('click', function () {
            button.parentElement.submit();
        })
    }
    );
});

// MESSAGGIO DI CONFERMA
document.addEventListener('DOMContentLoaded', function () {

    var successAlert = document.getElementById('success-alert');

    if (successAlert) {
        setTimeout(function () {
            successAlert.style.display = 'none';;
        }, 3000);
    }

});

document.addEventListener('DOMContentLoaded', function () {
    const addressInput = document.getElementById('address');
    const citySearch = document.getElementById('citySearch');
    const suggestionsList = document.getElementById('suggestions');
    const latInput = document.getElementById('lat');
    const longInput = document.getElementById('long');
    const TOMTOM_API_KEY = 'N4TIi8FzWNZv1sUqEUsREdKHYaG6HhSU';

    let map, marker, activeIndex = -1, suggestionsData = [];

    // Funzione per inizializzare la mappa con zoom dinamico
    function initializeMap(latitude, longitude, zoomLevel) {
        map = tt.map({
            key: TOMTOM_API_KEY,
            container: 'map',
            center: [longitude, latitude],
            zoom: zoomLevel
        });
        marker = new tt.Marker().setLngLat([longitude, latitude]).addTo(map);
    }

    // Funzione per aggiornare la posizione del marcatore sulla mappa con zoom maggiore
    function updateMap(latitude, longitude, zoomLevel = 15) {
        map.setCenter([longitude, latitude]);
        map.setZoom(zoomLevel); // Imposta un nuovo livello di zoom
        marker.setLngLat([longitude, latitude]);
    }

    // Funzione per ottenere suggerimenti dall'API TomTom
    function fetchSuggestions(query, callback) {
        fetch(`https://api.tomtom.com/search/2/search/${encodeURIComponent(query)}.json?key=${TOMTOM_API_KEY}&countrySet=IT&typeahead=true&limit=5&entityType=Municipality`)
            .then(response => response.json())
            .then(data => {
                suggestionsData = data.results.sort((a, b) => {
                    const startsWithA = a.address.freeformAddress.toLowerCase().startsWith(query.toLowerCase());
                    const startsWithB = b.address.freeformAddress.toLowerCase().startsWith(query.toLowerCase());
                    if (startsWithA && !startsWithB) return -1;
                    if (!startsWithA && startsWithB) return 1;
                    return a.address.freeformAddress.length - b.address.freeformAddress.length;
                });
                callback(suggestionsData);
            })
            .catch(error => console.error('Errore nella richiesta di suggerimenti:', error));
    }

    // Funzione per mostrare i suggerimenti
    function displaySuggestions(data) {
        suggestionsList.innerHTML = '';
        data.forEach((result, index) => {
            const suggestionItem = document.createElement('div');
            suggestionItem.classList.add('suggestion-item');
            suggestionItem.innerHTML = `<strong>${result.address.freeformAddress}</strong>`;
            suggestionItem.setAttribute('data-index', index);

            suggestionItem.addEventListener('click', function () {
                selectSuggestion(result);
            });

            suggestionsList.appendChild(suggestionItem);
        });
    }

    // Funzione per gestire la selezione del suggerimento e zoomare
    function selectSuggestion(result) {
        const latitude = result.position.lat;
        const longitude = result.position.lon;
        const address = result.address.freeformAddress;

        if (addressInput) {
            addressInput.value = address;
            latInput.value = latitude;
            longInput.value = longitude;
            suggestionsList.innerHTML = '';
            updateMap(latitude, longitude, 15); // Aggiorna la mappa con un livello di zoom maggiore
        } else if (citySearch) {
            citySearch.value = address;
            suggestionsList.innerHTML = '';
            window.location.href = `/properties?latitude=${latitude}&longitude=${longitude}`;
        }
    }

    // Funzione per gestire la navigazione con tastiera
    function handleKeyDown(event, inputElement) {
        const suggestionItems = suggestionsList.getElementsByClassName('suggestion-item');

        if (event.key === 'Enter') {
            if (inputElement.value.trim() === '' && citySearch) {
                fetchAllProperties();
            } else if (suggestionItems.length > 0 && activeIndex >= 0) {
                selectSuggestion(suggestionsData[activeIndex]);
            }
            event.preventDefault();
        } else if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            if (suggestionItems.length > 0) {
                activeIndex = (event.key === 'ArrowDown')
                    ? (activeIndex + 1) % suggestionItems.length
                    : (activeIndex - 1 + suggestionItems.length) % suggestionItems.length;
                setActiveSuggestion(suggestionItems, activeIndex);
                event.preventDefault();
            }
        }
    }

    // Funzione per evidenziare il suggerimento attivo
    function setActiveSuggestion(suggestionItems, index) {
        Array.from(suggestionItems).forEach((item, i) => {
            item.classList.toggle('active', i === index);
        });
    }

    // Funzione per mostrare tutte le proprietà quando l'input è vuoto
    function fetchAllProperties() {
        window.location.href = `/properties`;
    }

    // Event listener per l'input di addressInput o citySearch
    if (addressInput || citySearch) {
        const inputElement = addressInput || citySearch;

        inputElement.addEventListener('input', function () {
            const query = inputElement.value.trim();
            activeIndex = -1;

            if (query.length > 1) {
                fetchSuggestions(query, displaySuggestions);
            } else {
                suggestionsList.innerHTML = '';
                suggestionsData = [];
            }
        });

        inputElement.addEventListener('keydown', function (event) {
            handleKeyDown(event, inputElement);
        });
    }

    // Inizializzazione della mappa con zoom dinamico
    if (latInput && longInput && latInput.value && longInput.value) {
        // Se latitudine e longitudine sono presenti, utilizza un livello di zoom più ravvicinato
        initializeMap(parseFloat(latInput.value), parseFloat(longInput.value), 15);
    } else {
        // Altrimenti, mostra una vista generale con zoom meno ravvicinato
        initializeMap(41.8719, 12.5674, 4);
    }
});


document.addEventListener('DOMContentLoaded', function () {
    // Rimuove gli spazi ai campi email e password prima dell'invio
    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', function (event) {
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');

        emailField.value = emailField.value.trim();
        passwordField.value = passwordField.value.trim();
    });

    // Configurazione della validazione con JustValidate
    const validation = new JustValidate('#loginForm');
    validation
        .addField('#email', [
            {
                rule: 'required',
                errorMessage: 'Enter a valid email address',
            },
            {
                rule: 'email',
                errorMessage: 'Enter a valid email address',
            },
        ])
        .addField('#password', [
            {
                rule: 'required',
                errorMessage: 'The password is required',
            },
            {
                rule: 'minLength',
                value: 8,
                errorMessage: 'The password must contain at least 8 characters',
            },
        ])
        .onSuccess((event) => {
            // Impedisce l'invio predefinito se ci sono errori backend
            loginForm.submit(); // Invio manuale dopo il successo della validazione
        });
});

document.addEventListener('DOMContentLoaded', function () {
    // Rimuove gli spazi ai campi prima dell'invio
    const registerForm = document.getElementById('registerForm');
    registerForm.addEventListener('submit', function (event) {
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('password-confirm');

        emailField.value = emailField.value.trim();
        passwordField.value = passwordField.value.trim();
        confirmPasswordField.value = confirmPasswordField.value.trim();
    });

    // Configurazione della validazione con JustValidate
    const validation = new JustValidate('#registerForm');
    validation
        // Campo opzionale `name`
        .addField('#name', [
            {
                rule: 'maxLength',
                value: 50,
                errorMessage: 'The name must not exceed 50 characters',
            },
        ])
        // Campo opzionale `surname`
        .addField('#surname', [
            {
                rule: 'maxLength',
                value: 50,
                errorMessage: 'The surname must not exceed 50 characters',
            },
        ])
        // Campo opzionale `birth_date`
        .addField('#birth_date', [
            {
                rule: 'customRegexp',
                value: /^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])-\d{4}$/,
                errorMessage: 'Enter a valid date in DD-MM-YYYY format',
            },
        ])
        // Campo obbligatorio `email`
        .addField('#email', [
            {
                rule: 'required',
                errorMessage: 'The email address is required',
            },
            {
                rule: 'email',
                errorMessage: 'Enter a valid email address',
            },
        ])
        // Campo obbligatorio `password`
        .addField('#password', [
            {
                rule: 'required',
                errorMessage: 'The password is required',
            },
            {
                rule: 'minLength',
                value: 8,
                errorMessage: 'The password must contain at least 8 characters',
            },
            {
                rule: 'maxLength',
                value: 255,
                errorMessage: 'The password must not exceed 255 characters',
            },
        ])
        // Campo `password-confirm` per confermare la password
        .addField('#password-confirm', [
            {
                rule: 'required',
                errorMessage: 'Password confirmation is required',
            },
            {
                validator: (value) => {
                    const password = document.getElementById('password').value;
                    return value === password;
                },
                errorMessage: 'The passwords do not match',
            },
        ])
        .onSuccess((event) => {
            // Impedisce l'invio predefinito se ci sono errori backend
            registerForm.submit(); // Invio manuale dopo il successo della validazione
        });
});

// CREATE VALIDATION
document.addEventListener('DOMContentLoaded', function () {
    // Rimuove gli spazi ai campi prima dell'invio
    const createForm = document.getElementById('createPropertyForm');
    if (createForm) {
        createForm.addEventListener('submit', function () {
            // Trim all relevant fields
            const fields = ['title', 'address', 'description'];
            fields.forEach((fieldId) => {
                const field = document.getElementById(fieldId);
                if (field) field.value = field.value.trim();
            });
        });

        // Configurazione della validazione con JustValidate
        const validation = new JustValidate(createForm);

        validation
            // Campo obbligatorio `title`
            .addField('#title', [
                {
                    rule: 'required',
                    errorMessage: 'The title is required',
                },
                {
                    rule: 'maxLength',
                    value: 50,
                    errorMessage: 'The title must not exceed 50 characters',
                },
            ])
            // Campo opzionale `cover_image` (solo file immagine con dimensione massima 4096KB)
            .addField('#cover_image', [
                {
                    rule: 'maxFilesCount',
                    value: 1,
                    errorMessage: 'Only one file is allowed',
                },
                {
                    rule: 'files',
                    value: {
                        files: {
                            extensions: ['jpg', 'jpeg', 'png', 'gif'],
                            maxSize: 4096 * 1024,
                        },
                    },
                    errorMessage: 'The file must be an image and not exceed 4MB',
                },
            ])
            // Campo opzionale `description`
            .addField('#description', [
                {
                    rule: 'maxLength',
                    value: 300,
                    errorMessage: 'The description must not exceed 300 characters',
                },
            ])
            // Campo obbligatorio `num_rooms`
            .addField('#num_rooms', [
                {
                    rule: 'required',
                    errorMessage: 'The number of rooms is required',
                },
                {
                    rule: 'number',
                    errorMessage: 'The number of rooms must be a valid number',
                },
                {
                    rule: 'minNumber',
                    value: 1,
                    errorMessage: 'The number of rooms must be at least 1',
                },
                {
                    rule: 'maxNumber',
                    value: 50,
                    errorMessage: 'The number of rooms must not exceed 50',
                },
            ])
            // Campo obbligatorio `num_beds`
            .addField('#num_beds', [
                {
                    rule: 'required',
                    errorMessage: 'The number of beds is required',
                },
                {
                    rule: 'number',
                    errorMessage: 'The number of beds must be a valid number',
                },
                {
                    rule: 'minNumber',
                    value: 1,
                    errorMessage: 'The number of beds must be at least 1',
                },
                {
                    rule: 'maxNumber',
                    value: 20,
                    errorMessage: 'The number of beds must not exceed 20',
                },
            ])
            // Campo obbligatorio `num_baths`
            .addField('#num_baths', [
                {
                    rule: 'required',
                    errorMessage: 'The number of bathrooms is required',
                },
                {
                    rule: 'number',
                    errorMessage: 'The number of bathrooms must be a valid number',
                },
                {
                    rule: 'minNumber',
                    value: 0,
                    errorMessage: 'The number of bathrooms must be at least 0',
                },
                {
                    rule: 'maxNumber',
                    value: 5,
                    errorMessage: 'The number of bathrooms must not exceed 5',
                },
            ])
            // Campo obbligatorio `mq` (Square Meters)
            .addField('#mq', [
                {
                    rule: 'required',
                    errorMessage: 'The square meters are required',
                },
                {
                    rule: 'number',
                    errorMessage: 'The square meters must be a valid number',
                },
                {
                    rule: 'minNumber',
                    value: 10,
                    errorMessage: 'The square meters must be at least 10',
                },
                {
                    rule: 'maxNumber',
                    value: 5000,
                    errorMessage: 'The square meters must not exceed 5000',
                },
            ])
            // Campo obbligatorio `address`
            .addField('#address', [
                {
                    rule: 'required',
                    errorMessage: 'The address is required',
                },
                {
                    rule: 'minLength',
                    value: 2,
                    errorMessage: 'The address must be at least 2 characters',
                },
                {
                    rule: 'maxLength',
                    value: 100,
                    errorMessage: 'The address must not exceed 100 characters',
                },
            ])
            // Campo obbligatorio `price`
            .addField('#price', [
                {
                    rule: 'required',
                    errorMessage: 'The price is required',
                },
                {
                    rule: 'number',
                    errorMessage: 'The price must be a valid number',
                },
                {
                    rule: 'minNumber',
                    value: 10,
                    errorMessage: 'The price must be at least 10',
                },
                {
                    rule: 'maxNumber',
                    value: 999999.99,
                    errorMessage: 'The price must not exceed 999999.99',
                },
            ])
            // Campo obbligatorio `type`
            .addField('#type', [
                {
                    rule: 'required',
                    errorMessage: 'The type is required',
                },
            ])
            // Campo obbligatorio `floor`
            .addField('#floor', [
                {
                    rule: 'required',
                    errorMessage: 'The floor is required',
                },
                {
                    rule: 'integer',
                    errorMessage: 'The floor must be an integer',
                },
            ])

            // Campo obbligatorio `services`
            .addField('[name="services[]"]', [
                {
                    validator: () => {
                        // Seleziona tutti i checkbox `services[]`
                        const checkboxes = document.querySelectorAll('[name="services[]"]');
                        // Verifica se almeno uno dei checkbox è selezionato
                        return Array.from(checkboxes).some(checkbox => checkbox.checked);
                    },
                    errorMessage: 'Please select at least one service',
                },
            ], {
                errorsContainer: document.getElementById('error-container-services')
            })

            // Campo radio `available`
            .addField('[name="available"]', [
                {
                    validator: () => {
                        const radios = document.querySelectorAll('[name="available"]');
                        return Array.from(radios).some(radio => radio.checked);
                    },
                    errorMessage: 'Please select availability',
                },
            ], {
                errorsContainer: document.getElementById('error-container-available')
            })

            .onSuccess(() => {
                createForm.submit();
            });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // Rimuove gli spazi ai campi prima dell'invio
    const createForm = document.getElementById('editPropertyForm');
    if (createForm) {
        createForm.addEventListener('submit', function () {
            // Trim all relevant fields
            const fields = ['title', 'address', 'description'];
            fields.forEach((fieldId) => {
                const field = document.getElementById(fieldId);
                if (field) field.value = field.value.trim();
            });
        });

        // Configurazione della validazione con JustValidate
        const validation = new JustValidate(createForm);

        validation
            // Campo obbligatorio `title`
            .addField('#title', [
                {
                    rule: 'required',
                    errorMessage: 'The title is required',
                },
                {
                    rule: 'maxLength',
                    value: 50,
                    errorMessage: 'The title must not exceed 50 characters',
                },
            ])
            // Campo opzionale `cover_image` (solo file immagine con dimensione massima 4096KB)
            .addField('#cover_image', [
                {
                    rule: 'maxFilesCount',
                    value: 1,
                    errorMessage: 'Only one file is allowed',
                },
                {
                    rule: 'files',
                    value: {
                        files: {
                            extensions: ['jpg', 'jpeg', 'png', 'gif'],
                            maxSize: 4096 * 1024,
                        },
                    },
                    errorMessage: 'The file must be an image and not exceed 4MB',
                },
            ])
            // Campo opzionale `description`
            .addField('#description', [
                {
                    rule: 'maxLength',
                    value: 300,
                    errorMessage: 'The description must not exceed 300 characters',
                },
            ])
            // Campo obbligatorio `num_rooms`
            .addField('#num_rooms', [
                {
                    rule: 'required',
                    errorMessage: 'The number of rooms is required',
                },
                {
                    rule: 'number',
                    errorMessage: 'The number of rooms must be a valid number',
                },
                {
                    rule: 'minNumber',
                    value: 1,
                    errorMessage: 'The number of rooms must be at least 1',
                },
                {
                    rule: 'maxNumber',
                    value: 50,
                    errorMessage: 'The number of rooms must not exceed 50',
                },
            ])
            // Campo obbligatorio `num_beds`
            .addField('#num_beds', [
                {
                    rule: 'required',
                    errorMessage: 'The number of beds is required',
                },
                {
                    rule: 'number',
                    errorMessage: 'The number of beds must be a valid number',
                },
                {
                    rule: 'minNumber',
                    value: 1,
                    errorMessage: 'The number of beds must be at least 1',
                },
                {
                    rule: 'maxNumber',
                    value: 20,
                    errorMessage: 'The number of beds must not exceed 20',
                },
            ])
            // Campo obbligatorio `num_baths`
            .addField('#num_baths', [
                {
                    rule: 'required',
                    errorMessage: 'The number of bathrooms is required',
                },
                {
                    rule: 'number',
                    errorMessage: 'The number of bathrooms must be a valid number',
                },
                {
                    rule: 'minNumber',
                    value: 0,
                    errorMessage: 'The number of bathrooms must be at least 0',
                },
                {
                    rule: 'maxNumber',
                    value: 5,
                    errorMessage: 'The number of bathrooms must not exceed 5',
                },
            ])
            // Campo obbligatorio `mq` (Square Meters)
            .addField('#mq', [
                {
                    rule: 'required',
                    errorMessage: 'The square meters are required',
                },
                {
                    rule: 'number',
                    errorMessage: 'The square meters must be a valid number',
                },
                {
                    rule: 'minNumber',
                    value: 10,
                    errorMessage: 'The square meters must be at least 10',
                },
                {
                    rule: 'maxNumber',
                    value: 5000,
                    errorMessage: 'The square meters must not exceed 5000',
                },
            ])
            // Campo obbligatorio `address`
            .addField('#address', [
                {
                    rule: 'required',
                    errorMessage: 'The address is required',
                },
                {
                    rule: 'minLength',
                    value: 2,
                    errorMessage: 'The address must be at least 2 characters',
                },
                {
                    rule: 'maxLength',
                    value: 100,
                    errorMessage: 'The address must not exceed 100 characters',
                },
            ])
            // Campo obbligatorio `price`
            .addField('#price', [
                {
                    rule: 'required',
                    errorMessage: 'The price is required',
                },
                {
                    rule: 'number',
                    errorMessage: 'The price must be a valid number',
                },
                {
                    rule: 'minNumber',
                    value: 10,
                    errorMessage: 'The price must be at least 10',
                },
                {
                    rule: 'maxNumber',
                    value: 999999.99,
                    errorMessage: 'The price must not exceed 999999.99',
                },
            ])
            // Campo obbligatorio `type`
            .addField('#type', [
                {
                    rule: 'required',
                    errorMessage: 'The type is required',
                },
            ])
            // Campo obbligatorio `floor`
            .addField('#floor', [
                {
                    rule: 'required',
                    errorMessage: 'The floor is required',
                },
                {
                    rule: 'integer',
                    errorMessage: 'The floor must be an integer',
                },
            ])

            // Campo obbligatorio `services`
            .addField('[name="services[]"]', [
                {
                    validator: () => {
                        // Seleziona tutti i checkbox `services[]`
                        const checkboxes = document.querySelectorAll('[name="services[]"]');
                        // Verifica se almeno uno dei checkbox è selezionato
                        return Array.from(checkboxes).some(checkbox => checkbox.checked);
                    },
                    errorMessage: 'Please select at least one service',
                },
            ], {
                errorsContainer: document.getElementById('error-container-services')
            })

            // Campo radio `available`
            .addField('[name="available"]', [
                {
                    validator: () => {
                        const radios = document.querySelectorAll('[name="available"]');
                        return Array.from(radios).some(radio => radio.checked);
                    },
                    errorMessage: 'Please select availability',
                },
            ], {
                errorsContainer: document.getElementById('error-container-available')
            })

            .onSuccess(() => {
                createForm.submit();
            });
    }
});
