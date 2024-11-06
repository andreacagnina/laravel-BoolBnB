import './bootstrap';
import '~resources/scss/app.scss';
import '~icons/bootstrap-icons.scss';
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
        ModalText.innerHTML = `Sei sicuro di volere cancellare questo immobile: <strong>${propertyName}</strong> ?`;

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


// confronta se le password sono uguali
function handleSubmit(event) {
    if (!validatePassword()) {
        event.preventDefault(); // Blocca l'invio del form se le password non coincidono
    }
}

function validatePassword() {
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password-confirm');
    const tooltip = document.getElementById('password-tooltip');

    if (password.value !== passwordConfirm.value) {
        passwordConfirm.classList.add('is-invalid');
        tooltip.style.display = 'block'; // Mostra la tooltip
        return false;
    } else {
        passwordConfirm.classList.remove('is-invalid');
        tooltip.style.display = 'none'; // Nascondi la tooltip
        return true;
    }
}

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
