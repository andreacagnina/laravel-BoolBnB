import './bootstrap';
import '~resources/scss/app.scss';
import '~icons/bootstrap-icons.scss';
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
        ModalText.innerHTML = `Sei sicuro di volere cancellare questo immobile: <strong>${propertyName}</strong> ?`;

        buttonDelete.addEventListener('click', function () {
            button.parentElement.submit();
        });
    });
});

// MESSAGGIO DI CONFERMA
document.addEventListener('DOMContentLoaded', function () {
    var successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(function () {
            successAlert.style.display = 'none';
        }, 3000);
    }
});

// Inizializzazione della mappa nella show/edit
document.addEventListener('DOMContentLoaded', function () {
    const mapContainer = document.getElementById('map');
    const latInput = document.getElementById('lat');
    const longInput = document.getElementById('long');

    if (mapContainer && latInput && longInput) {
        const lat = parseFloat(latInput.value);
        const long = parseFloat(longInput.value);

        const map = tt.map({
            key: TOMTOM_API_KEY,
            container: 'map',
            center: [long, lat],
            zoom: 15
        });

        new tt.Marker().setLngLat([long, lat]).addTo(map);
    }
});

// Funzioni di filtro e ricerca sulla homepage
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
    const filterModal = new bootstrap.Modal(filterModalElement); // Istanza della modale

    let suggestionsData = [];
    let selectedAddress = null;
    let activeSuggestionIndex = -1;

    // Funzione per resettare i filtri e chiudere la modale
    resetFiltersButton.addEventListener('click', function () {
        roomsInput.value = '1';
        bedsInput.value = '1';
        radiusInput.value = '20';
        document.querySelectorAll('input[name="services[]"]').forEach((checkbox) => {
            checkbox.checked = false;
        });
        citySearch.value = '';
        selectedAddress = null;
        fetchProperties(); // Aggiorna i risultati
        filterModal.hide(); // Chiude la modale
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

    // Funzione per mostrare i suggerimenti sotto l'input
    function displaySuggestions() {
        suggestionsList.innerHTML = '';
        suggestionsData.forEach((result, index) => {
            const suggestionItem = document.createElement('a');
            suggestionItem.classList.add('list-group-item', 'list-group-item-action');
            suggestionItem.textContent = result.address.freeformAddress;
            suggestionItem.addEventListener('click', function () {
                selectSuggestion(index);
            });
            suggestionsList.appendChild(suggestionItem);
        });
        suggestionsList.style.display = suggestionsData.length ? 'block' : 'none';
    }

    // Funzione per gestire la selezione del suggerimento
    function selectSuggestion(index) {
        const result = suggestionsData[index];
        selectedAddress = result;
        citySearch.value = result.address.freeformAddress;
        suggestionsList.innerHTML = '';
        suggestionsList.style.display = 'none';
    }

    // Event listener per il campo di ricerca per mostrare i suggerimenti
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

    // Funzione per navigare nei suggerimenti con frecce e Enter
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

    // Funzione per evidenziare il suggerimento attivo
    function updateActiveSuggestion() {
        const items = suggestionsList.querySelectorAll('.list-group-item');
        items.forEach((item, index) => {
            item.classList.toggle('active', index === activeSuggestionIndex);
        });
    }

    // Funzione per eseguire la ricerca degli appartamenti
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

    // Funzione per aggiornare i risultati nel DOM
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
