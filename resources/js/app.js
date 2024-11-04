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

document.addEventListener('DOMContentLoaded', function () {
    const addressInput = document.getElementById('address');
    const suggestionsList = document.getElementById('suggestions');
    const latInput = document.getElementById('lat'); // Campo nascosto per latitudine
    const longInput = document.getElementById('long'); // Campo nascosto per longitudine

    const TOMTOM_API_KEY = 'N4TIi8FzWNZv1sUqEUsREdKHYaG6HhSU'; // Inserisci la tua chiave API di TomTom

    let map; // Variabile per la mappa
    let marker; // Variabile per il marcatore

    // Funzione per inizializzare la mappa
    function initializeMap(latitude = 41.8719, longitude = 12.5674, zoomLevel = 4.4) { // Italia come default
        map = tt.map({
            key: TOMTOM_API_KEY,
            container: 'map',
            center: [longitude, latitude],
            zoom: zoomLevel
        });

        // Crea il marcatore e posizionalo al centro (inizialmente sull’Italia)
        marker = new tt.Marker().setLngLat([longitude, latitude]).addTo(map);
    }

    // Funzione per aggiornare il marcatore e la posizione della mappa
    function updateMap(latitude, longitude) {
        map.setCenter([longitude, latitude]);
        marker.setLngLat([longitude, latitude]);
    }

    // Suggerimenti di indirizzo usando TomTom Search API
    if (addressInput) {
        addressInput.addEventListener('input', function () {
            const query = addressInput.value;
            if (query.length > 1) {
                fetch(`https://api.tomtom.com/search/2/search/${encodeURIComponent(query)}.json?key=${TOMTOM_API_KEY}&countrySet=IT`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsList.innerHTML = ''; // Pulisce i suggerimenti precedenti

                        if (data.results && data.results.length > 0) {
                            data.results.forEach((result, index) => {
                                const fullAddress = result.address.freeformAddress;
                                const suggestionItem = document.createElement('div');
                                suggestionItem.classList.add('suggestion-item');
                                suggestionItem.innerHTML = `<strong>${fullAddress}</strong>`;
                                suggestionItem.setAttribute('data-index', index);

                                // Click sul suggerimento
                                suggestionItem.addEventListener('click', function () {
                                    selectSuggestion(result);
                                });
                                suggestionsList.appendChild(suggestionItem);
                            });
                        }
                    })
                    .catch(error => console.error('Errore nella richiesta di suggerimenti:', error));
            } else {
                suggestionsList.innerHTML = '';
            }
        });
    }

    // Funzione per gestire la selezione del suggerimento
    function selectSuggestion(result) {
        const latitude = result.position.lat;
        const longitude = result.position.lon;

        latInput.value = latitude;
        longInput.value = longitude;
        addressInput.value = result.address.freeformAddress;
        suggestionsList.innerHTML = '';

        // Aggiorna la mappa con le nuove coordinate
        if (map) {
            updateMap(latitude, longitude);
            map.setZoom(15); // Zoom più ravvicinato
        } else {
            initializeMap(latitude, longitude, 15);
        }
    }

    // Inizializza la mappa: su Italia se non ci sono coordinate, oppure su coordinate salvate
    if (latInput && longInput && latInput.value && longInput.value) {
        initializeMap(parseFloat(latInput.value), parseFloat(longInput.value), 15);
    } else {
        initializeMap(); // Visualizzazione generale sull’Italia con zoom per tutto il paese
    }
});
