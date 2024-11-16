import './bootstrap';
import '~resources/scss/app.scss';
import '~icons/bootstrap-icons.scss';
import JustValidate from 'just-validate';
import * as bootstrap from 'bootstrap';
import.meta.glob(['../img/**']);
import Turbolinks from 'turbolinks'; // Importa Turbolinks

Turbolinks.start(); // Avvia Turbolinks

// Chiave API TomTom
const TOMTOM_API_KEY = 'N4TIi8FzWNZv1sUqEUsREdKHYaG6HhSU';

// Funzione per ottenere suggerimenti per l'indirizzo
function fetchAddressSuggestions(query) {
    return fetch(`https://api.tomtom.com/search/2/search/${encodeURIComponent(query)}.json?key=${TOMTOM_API_KEY}&countrySet=IT&typeahead=true&limit=5`)
        .then(response => response.json())
        .then(data => data.results)
        .catch(error => {
            console.error('Errore nel recuperare i suggerimenti degli indirizzi:', error);
            return [];
        });
}

// Modale di conferma per la delete
document.addEventListener('DOMContentLoaded', function () {
    const delete_buttons = document.querySelectorAll('.delete');
    delete_buttons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const modal = document.getElementById('deleteModal');
            const bootstrap_modal = new bootstrap.Modal(modal);
            bootstrap_modal.show();

            const buttonDelete = modal.querySelector('.confirm-delete');
            const modalText = modal.querySelector('#modal_text');
            modalText.innerHTML = 'Are you sure you want to delete this item?';

            buttonDelete.addEventListener('click', function () {
                button.parentElement.submit();
                bootstrap_modal.hide(); // Nasconde la modale dopo la conferma di eliminazione
            });
        });
    });

    // Assicura la rimozione dell'overlay quando la modale viene chiusa
    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function () {
        const modalBackdrop = document.querySelector('.modal-backdrop');
        if (modalBackdrop) {
            modalBackdrop.remove();
        }
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

// Inizializzazione della mappa per la pagina della show
document.addEventListener('DOMContentLoaded', function () {
    const mapContainer = document.getElementById('map');
    const latInput = document.getElementById('lat');
    const longInput = document.getElementById('long');

    if (mapContainer && latInput && longInput && !document.getElementById('editPropertyForm')) {
        const lat = parseFloat(latInput.value) || 41.8719;
        const long = parseFloat(longInput.value) || 12.5674;

        const map = tt.map({
            key: TOMTOM_API_KEY,
            container: 'map',
            center: [long, lat],
            zoom: latInput.value && longInput.value ? 15 : 4
        });
        const marker = new tt.Marker().setLngLat([long, lat]).addTo(map);
    }
});

// Inizializzazione della mappa e suggerimenti indirizzo per le pagine create/edit
document.addEventListener('DOMContentLoaded', function () {
    const mapContainer = document.getElementById('map');
    const latInput = document.getElementById('lat');
    const longInput = document.getElementById('long');
    const addressInput = document.getElementById('address');
    const suggestionsListCreateEdit = document.getElementById('suggestions-create-edit');

    if (mapContainer && latInput && longInput && addressInput && suggestionsListCreateEdit && (document.getElementById('editPropertyForm') || document.getElementById('createPropertyForm'))) {
        const lat = parseFloat(latInput.value) || 41.8719;
        const long = parseFloat(longInput.value) || 12.5674;

        const map = tt.map({
            key: TOMTOM_API_KEY,
            container: 'map',
            center: [long, lat],
            zoom: latInput.value && longInput.value ? 15 : 4
        });
        const marker = new tt.Marker().setLngLat([long, lat]).addTo(map);

        function updateMap(latitude, longitude, zoomLevel = 15) {
            map.setCenter([longitude, latitude]);
            map.setZoom(zoomLevel);
            marker.setLngLat([longitude, latitude]);
        }

        let suggestionsData = [];
        let selectedAddress = null;
        let activeSuggestionIndex = -1;

        addressInput.addEventListener('input', function () {
            const query = addressInput.value.trim();
            selectedAddress = null;
            activeSuggestionIndex = -1;
            if (query.length > 1) {
                fetchAddressSuggestions(query).then(results => {
                    suggestionsData = results;
                    displayAddressSuggestions();
                });
            } else {
                suggestionsListCreateEdit.innerHTML = '';
                suggestionsListCreateEdit.style.display = 'none';
            }
        });

        function displayAddressSuggestions() {
            suggestionsListCreateEdit.innerHTML = '';
            suggestionsData.forEach((result, index) => {
                const suggestionItem = document.createElement('a');
                suggestionItem.classList.add('list-group-item', 'list-group-item-action');
                suggestionItem.textContent = result.address.freeformAddress;
                suggestionItem.addEventListener('click', () => selectAddressSuggestion(index));
                suggestionsListCreateEdit.appendChild(suggestionItem);
            });
            suggestionsListCreateEdit.style.display = suggestionsData.length ? 'block' : 'none';
        }

        function selectAddressSuggestion(index) {
            const result = suggestionsData[index];
            selectedAddress = result;
            addressInput.value = result.address.freeformAddress;
            latInput.value = result.position.lat;
            longInput.value = result.position.lon;
            updateMap(result.position.lat, result.position.lon);
            suggestionsListCreateEdit.innerHTML = '';
            suggestionsListCreateEdit.style.display = 'none';
        }

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
                suggestionsListCreateEdit.style.display = 'none';
            }
        });

        function updateActiveSuggestion() {
            const items = suggestionsListCreateEdit.querySelectorAll('.list-group-item');
            items.forEach((item, index) => item.classList.toggle('active', index === activeSuggestionIndex));
        }

        const editForm = document.getElementById('editPropertyForm');
        if (editForm) {
            editForm.addEventListener('submit', function (event) {
                if (!latInput.value || !longInput.value) {
                    event.preventDefault();
                    alert('Seleziona un indirizzo valido dai suggerimenti.');
                }
            });
        }
    }
});

// Inizializzazione della funzionalità di ricerca e mappa per la homepage
document.addEventListener('DOMContentLoaded', function () {
    const citySearchInput = document.getElementById('citySearch');
    const suggestionsListHome = document.getElementById('suggestions');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const resultsContainer = document.getElementById('resultsContainer');

    if (citySearchInput && suggestionsListHome && latitudeInput && longitudeInput && resultsContainer) {
        let suggestionsDataHome = [];
        let activeSuggestionIndexHome = -1;

        citySearchInput.addEventListener('input', function () {
            const query = citySearchInput.value.trim();
            activeSuggestionIndexHome = -1;
            if (query.length > 1) {
                fetchAddressSuggestions(query).then(results => {
                    suggestionsDataHome = results;
                    displayAddressSuggestionsHome();
                });
            } else {
                suggestionsListHome.innerHTML = '';
                suggestionsListHome.style.display = 'none';
            }
        });

        function displayAddressSuggestionsHome() {
            suggestionsListHome.innerHTML = '';
            suggestionsDataHome.forEach((result, index) => {
                const suggestionItem = document.createElement('a');
                suggestionItem.classList.add('list-group-item', 'list-group-item-action');
                suggestionItem.textContent = result.address.freeformAddress;
                suggestionItem.addEventListener('click', () => selectAddressSuggestionHome(index));
                suggestionsListHome.appendChild(suggestionItem);
            });
            suggestionsListHome.style.display = suggestionsDataHome.length ? 'block' : 'none';
        }

        function selectAddressSuggestionHome(index) {
            const result = suggestionsDataHome[index];
            citySearchInput.value = result.address.freeformAddress;
            latitudeInput.value = result.position.lat;
            longitudeInput.value = result.position.lon;
            suggestionsListHome.innerHTML = '';
            suggestionsListHome.style.display = 'none';
        }

        citySearchInput.addEventListener('keydown', function (event) {
            const items = suggestionsListHome.querySelectorAll('.list-group-item');
            if (event.key === 'ArrowDown') {
                if (activeSuggestionIndexHome < items.length - 1) {
                    activeSuggestionIndexHome++;
                    updateActiveSuggestionHome();
                }
            } else if (event.key === 'ArrowUp') {
                if (activeSuggestionIndexHome > 0) {
                    activeSuggestionIndexHome--;
                    updateActiveSuggestionHome();
                }
            } else if (event.key === 'Enter') {
                event.preventDefault();
                if (activeSuggestionIndexHome >= 0) {
                    selectAddressSuggestionHome(activeSuggestionIndexHome);
                } else if (suggestionsDataHome.length > 0) {
                    selectAddressSuggestionHome(0);
                }
                suggestionsListHome.style.display = 'none';
                performSearch();
            }
        });

        function updateActiveSuggestionHome() {
            const items = suggestionsListHome.querySelectorAll('.list-group-item');
            items.forEach((item, index) => item.classList.toggle('active', index === activeSuggestionIndexHome));
        }

        function performSearch() {
            const latitude = latitudeInput ? latitudeInput.value : null;
            const longitude = longitudeInput ? longitudeInput.value : null;
            const radius = document.getElementById('radius').value || 20;
            const minRooms = document.getElementById('rooms').value || 1;
            const minBeds = document.getElementById('beds').value || 1;

            const selectedServices = Array.from(document.querySelectorAll('[name="services[]"]:checked'))
                .map(checkbox => checkbox.value);

            const params = new URLSearchParams({
                radius: radius,
                rooms: minRooms,
                beds: minBeds,
            });

            if (latitude && longitude) {
                params.append('latitude', latitude);
                params.append('longitude', longitude);
            }

            selectedServices.forEach(serviceId => {
                params.append('services[]', serviceId);
            });

            fetch(`/?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then(response => response.json())
                .then(data => {
                    updateResults(data.properties);
                })
                .catch(error => console.error('Errore nella ricerca:', error));
        }

        function updateResults(properties) {
            resultsContainer.innerHTML = '';

            if (properties.length === 0) {
                resultsContainer.innerHTML = '<p>No properties found within the specified criteria.</p>';
                return;
            }

            properties.forEach(property => {
                const colDiv = document.createElement('div');
                colDiv.classList.add('col-md-4', 'mb-4');

                const cardDiv = document.createElement('div');
                cardDiv.classList.add('card', 'h-100', 'shadow-sm');
                if (property.sponsored) {
                    cardDiv.classList.add('border-success');
                }

                if (property.sponsored) {
                    const badge = document.createElement('span');
                    badge.classList.add('badge', 'bg-success', 'position-absolute', 'top-0', 'end-0', 'm-2');
                    badge.textContent = 'Sponsored';
                    cardDiv.appendChild(badge);
                }

                const imgDiv = document.createElement('div');
                imgDiv.classList.add('overflow-hidden');
                imgDiv.style.height = '200px';

                const img = document.createElement('img');
                img.src = property.cover_image_url;
                img.classList.add('card-img-top', 'w-100', 'h-100');
                img.style.objectFit = 'cover';
                img.alt = property.title;

                imgDiv.appendChild(img);
                cardDiv.appendChild(imgDiv);

                const cardBody = document.createElement('div');
                cardBody.classList.add('card-body', 'd-flex', 'flex-column');

                const title = document.createElement('h5');
                title.classList.add('card-title');
                title.textContent = property.title;

                const description = document.createElement('p');
                description.classList.add('card-text', 'text-muted');
                description.textContent = property.description.length > 60 ? property.description.substring(0, 60) + '...' : property.description;

                const price = document.createElement('p');
                price.classList.add('card-text');
                price.innerHTML = `<strong>Price:</strong> ${property.price.toFixed(2).replace('.', ',')}&euro;`;

                const location = document.createElement('p');
                location.classList.add('card-text');
                location.innerHTML = `<strong>Location:</strong> ${property.address}`;

                cardBody.appendChild(title);
                cardBody.appendChild(description);
                cardBody.appendChild(price);
                cardBody.appendChild(location);

                if (property.distance !== undefined) {
                    const distance = document.createElement('p');
                    distance.classList.add('card-text');
                    distance.innerHTML = `<strong>Distance:</strong> ${property.distance} km`;
                    cardBody.appendChild(distance);
                }

                const detailsLink = document.createElement('a');
                detailsLink.href = `/properties/${property.slug}`;
                detailsLink.classList.add('mt-auto', 'btn', 'btn-outline-primary');
                detailsLink.textContent = 'View Details';

                cardBody.appendChild(detailsLink);
                cardDiv.appendChild(cardBody);
                colDiv.appendChild(cardDiv);
                resultsContainer.appendChild(colDiv);
            });
        }

        const searchButton = document.getElementById('searchButton');
        if (searchButton) {
            searchButton.addEventListener('click', performSearch);
        }

        const filterModalElement = document.getElementById('filterModal');
        const filterModal = new bootstrap.Modal(filterModalElement);

        const applyFiltersButton = document.getElementById('applyFiltersButton');
        if (applyFiltersButton) {
            applyFiltersButton.addEventListener('click', performSearch);
        }

        const resetFiltersButton = document.getElementById('resetFiltersButton');
        if (resetFiltersButton) {
            resetFiltersButton.addEventListener('click', function () {
                document.getElementById('radius').value = 20;
                document.getElementById('rooms').value = 1;
                document.getElementById('beds').value = 1;
                document.querySelectorAll('[name="services[]"]').forEach(service => service.checked = false);

                if (citySearchInput) {
                    citySearchInput.value = '';
                }

                if (latitudeInput && longitudeInput) {
                    latitudeInput.value = '';
                    longitudeInput.value = '';
                }

                filterModal.hide();
                performSearch();
            });
        }
    }
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

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        trimFormFields(loginForm, ['email', 'password']);
        const validation = new JustValidate('#loginForm');
        validation
            .addField('#email', [{ rule: 'required', errorMessage: 'Enter a valid email address' }, { rule: 'email', errorMessage: 'Enter a valid email address' }])
            .addField('#password', [{ rule: 'required', errorMessage: 'The password is required' }, { rule: 'minLength', value: 8, errorMessage: 'The password must contain at least 8 characters' }])
            .onSuccess(() => loginForm.submit());
    }

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        trimFormFields(registerForm, ['email', 'password', 'password-confirm']);
        const validation = new JustValidate('#registerForm');
        validation
            .addField('#name', [{ rule: 'maxLength', value: 50, errorMessage: 'The name must not exceed 50 characters' }])
            .addField('#surname', [{ rule: 'maxLength', value: 50, errorMessage: 'The surname must not exceed 50 characters' }])
            // .addField('#birth_date', [{ rule: 'customRegexp', value: /^(0[1-9]|[12][0-9]|3[01])\s*\/\s*(0[1-9]|1[0-2])\s*\/\s*\d{4}$/, errorMessage: 'Enter a valid date in DD/MM/YYYY format' }])
            .addField('#email', [{ rule: 'required', errorMessage: 'The email address is required' }, { rule: 'email', errorMessage: 'Enter a valid email address' }])
            .addField('#password', [{ rule: 'required', errorMessage: 'The password is required' }, { rule: 'minLength', value: 8, errorMessage: 'The password must contain at least 8 characters' }, { rule: 'maxLength', value: 255, errorMessage: 'The password must not exceed 255 characters' }])
            .addField('#password-confirm', [{ rule: 'required', errorMessage: 'Password confirmation is required' }, { validator: value => value === document.getElementById('password').value, errorMessage: 'The passwords do not match' }])
            .onSuccess(() => registerForm.submit());
    }


    ['createPropertyForm', 'editPropertyForm'].forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            trimFormFields(form, ['title', 'address', 'description']);
            const validation = new JustValidate(form);
            validation
                .addField('#title', [{ rule: 'required', errorMessage: 'The title is required' }, { rule: 'maxLength', value: 50, errorMessage: 'The title must not exceed 50 characters' }], { errorsContainer: document.getElementById('error-container-title') })

                .addField('#cover_image', [{ rule: 'maxFilesCount', value: 1, errorMessage: 'Only one file is allowed' }, { rule: 'files', value: { files: { extensions: ['jpg', 'jpeg', 'png', 'gif'], maxSize: 4096 * 1024 } }, errorMessage: 'The file must be an image and not exceed 4MB' }], { errorsContainer: document.getElementById('error-container-cover_image') })

                .addField('#description', [{ rule: 'maxLength', value: 300, errorMessage: 'The description must not exceed 300 characters' }], { errorsContainer: document.getElementById('error-container-description') })

                .addField('#num_rooms', [{ rule: 'required', errorMessage: 'The number of rooms is required' }, { rule: 'number', errorMessage: 'The number of rooms must be a valid number' }, { rule: 'minNumber', value: 1, errorMessage: 'The number of rooms must be at least 1' }, { rule: 'maxNumber', value: 50, errorMessage: 'The number of rooms must not exceed 50' }], { errorsContainer: document.getElementById('error-container-num_rooms') })

                .addField('#num_beds', [{ rule: 'required', errorMessage: 'The number of beds is required' }, { rule: 'number', errorMessage: 'The number of beds must be a valid number' }, { rule: 'minNumber', value: 1, errorMessage: 'The number of beds must be at least 1' }, { rule: 'maxNumber', value: 20, errorMessage: 'The number of beds must not exceed 20' }], { errorsContainer: document.getElementById('error-container-num_beds') })

                .addField('#num_baths', [{ rule: 'required', errorMessage: 'The number of bathrooms is required' }, { rule: 'number', errorMessage: 'The number of bathrooms must be a valid number' }, { rule: 'minNumber', value: 0, errorMessage: 'The number of bathrooms must be at least 0' }, { rule: 'maxNumber', value: 5, errorMessage: 'The number of bathrooms must not exceed 5' }], { errorsContainer: document.getElementById('error-container-num_baths') })

                .addField('#mq', [{ rule: 'required', errorMessage: 'The square meters are required' }, { rule: 'number', errorMessage: 'The square meters must be a valid number' }, { rule: 'minNumber', value: 10, errorMessage: 'The square meters must be at least 10' }, { rule: 'maxNumber', value: 5000, errorMessage: 'The square meters must not exceed 5000' }], { errorsContainer: document.getElementById('error-container-mq') })

                .addField('#address', [{ rule: 'required', errorMessage: 'The address is required' }, { rule: 'minLength', value: 2, errorMessage: 'The address must be at least 2 characters' }, { rule: 'maxLength', value: 100, errorMessage: 'The address must not exceed 100 characters' }], { errorsContainer: document.getElementById('error-container-address') })

                .addField('#price', [{ rule: 'required', errorMessage: 'The price is required' }, { rule: 'number', errorMessage: 'The price must be a valid number' }, { rule: 'minNumber', value: 10, errorMessage: 'The price must be at least 10' }, { rule: 'maxNumber', value: 999999.99, errorMessage: 'The price must not exceed 999999.99' }], { errorsContainer: document.getElementById('error-container-price') })

                .addField('#type', [{ rule: 'required', errorMessage: 'The type is required' }], { errorsContainer: document.getElementById('error-container-type') })

                .addField('#floor', [{ rule: 'required', errorMessage: 'The floor is required' }, { rule: 'integer', errorMessage: 'The floor must be an integer' }], { errorsContainer: document.getElementById('error-container-floor') })

                .addField('[name="services[]"]', [{ validator: () => Array.from(document.querySelectorAll('[name="services[]"]')).some(checkbox => checkbox.checked), errorMessage: 'Please select at least one service' }], { errorsContainer: document.getElementById('error-container-services') })

                .addField('[name="available"]', [{ validator: () => Array.from(document.querySelectorAll('[name="available"]')).some(radio => radio.checked), errorMessage: 'Please select availability' }], { errorsContainer: document.getElementById('error-container-available') })
                .onSuccess(() => form.submit());
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const sponsorSelect = document.getElementById('sponsor-select');
    const sponsorIdInput = document.getElementById('sponsor_id');
    const payButton = document.getElementById('pay-button');
    const container = document.getElementById('dropin-container');

    // Disabilita il pulsante di pagamento all'inizio
    payButton.disabled = true;

    // Aggiungi la classe di "loading" al pulsante durante il caricamento
    function setLoadingState(isLoading) {
        payButton.disabled = isLoading;
        payButton.textContent = isLoading ? "Loading..." : "Pay with Braintree";
    }

    // Abilita il pulsante solo quando viene selezionata un'opzione valida
    sponsorSelect.addEventListener('change', function () {
        sponsorIdInput.value = sponsorSelect.value;

        if (sponsorSelect.value) {
            payButton.disabled = false;
            container.classList.remove('d-none');
        } else {
            payButton.disabled = true;
            container.classList.add('d-none');
        }
    });

    // Funzione per ottenere il token Braintree
    function fetchBraintreeToken() {
        setLoadingState(true);

        fetch("/admin/braintree/token")
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                braintree.dropin.create({
                    authorization: data.token,
                    container: '#dropin-container'
                }, function (createErr, instance) {
                    setLoadingState(false);

                    if (createErr) {
                        console.error('Errore nella creazione di Braintree Drop-in:', createErr);
                        return;
                    }

                    payButton.addEventListener('click', function () {
                        setLoadingState(true);

                        instance.requestPaymentMethod(function (err, payload) {
                            if (err) {
                                console.error('Errore nella richiesta del metodo di pagamento:', err);
                                setLoadingState(false);
                                return;
                            }

                            // Imposta il nonce nel campo nascosto del form
                            document.getElementById('payment-method-nonce').value = payload.nonce;

                            // Invia il form
                            document.getElementById('payment-form').submit();
                        });
                    });
                });
            })
            .catch(function (error) {
                console.error('Errore nel recupero del token Braintree:', error);
                setLoadingState(false);
            });
    }

    // Chiamata per ottenere il token Braintree al caricamento della pagina
    fetchBraintreeToken();
});

//countdown
document.addEventListener('DOMContentLoaded', function () {
    const endDateElement = document.getElementById('end-date');
    const countdownElement = document.getElementById('countdown');

    const endDateText = endDateElement.textContent.trim(); // Trim spaces for safety
    if (endDateText !== 'N/A') {
        // Convert "DD-MM-YYYY HH:mm" to "YYYY-MM-DDTHH:mm:ss" for compatibility
        const [datePart, timePart] = endDateText.split(' ');
        const [day, month, year] = datePart.split('-');
        const formattedDate = `${year}-${month}-${day}T${timePart}`;
        const endDate = new Date(formattedDate);

        if (isNaN(endDate.getTime())) {
            countdownElement.textContent = 'Invalid end date format';
            return;
        }

        function formatDateToDMY(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function updateCountdown() {
            const now = new Date();
            const timeDifference = endDate - now;

            if (timeDifference <= 0) {
                countdownElement.textContent = 'Sponsorship has ended';
                return;
            }

            const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

            const formattedNow = formatDateToDMY(now);
            countdownElement.textContent = `${days} days ${hours} hours ${minutes} minutes ${seconds} seconds remaining`;
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    } else {
        countdownElement.textContent = 'No end date available';
    }
});


