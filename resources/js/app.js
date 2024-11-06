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
    const suggestionsList = document.getElementById('suggestions');
    const latInput = document.getElementById('lat'); // Campo nascosto per latitudine
    const longInput = document.getElementById('long'); // Campo nascosto per longitudine
    const TOMTOM_API_KEY = 'N4TIi8FzWNZv1sUqEUsREdKHYaG6HhSU';

    let map; // Variabile per la mappa
    let marker; // Variabile per il marcatore
    let activeIndex = -1; // Indice del suggerimento attivo
    let suggestionsData = []; // Variabile per memorizzare i dati dei suggerimenti

    // Funzione per inizializzare la mappa
    function initializeMap(latitude = 41.8719, longitude = 12.5674, zoomLevel = 4.4) {
        map = tt.map({
            key: TOMTOM_API_KEY,
            container: 'map',
            center: [longitude, latitude],
            zoom: zoomLevel
        });
        marker = new tt.Marker().setLngLat([longitude, latitude]).addTo(map);
    }

    // Funzione per aggiornare il marcatore e la posizione della mappa
    function updateMap(latitude, longitude) {
        map.setCenter([longitude, latitude]);
        marker.setLngLat([longitude, latitude]);
    }

    // Funzione per mostrare tutti i risultati quando l'input è vuoto
    function fetchAllProperties() {
        initializeMap(); // Visualizza la vista iniziale su Italia
        suggestionsList.innerHTML = ''; // Pulisce i suggerimenti
    }

    // Suggerimenti di indirizzo usando TomTom Search API
    if (addressInput) {
        addressInput.addEventListener('input', function () {
            const query = addressInput.value.trim();
            activeIndex = -1; // Resetta l'indice ogni volta che cambia l'input

            if (query.length > 1) {
                fetch(`https://api.tomtom.com/search/2/search/${encodeURIComponent(query)}.json?key=${TOMTOM_API_KEY}&countrySet=IT&entityType=Municipality`)
                    .then(response => response.json())
                    .then(data => {
                        // Ordina i suggerimenti per pertinenza
                        suggestionsData = data.results
                            .sort((a, b) => {
                                // Priorità alle corrispondenze che iniziano con la query
                                const startsWithA = a.address.freeformAddress.toLowerCase().startsWith(query.toLowerCase());
                                const startsWithB = b.address.freeformAddress.toLowerCase().startsWith(query.toLowerCase());
                                if (startsWithA && !startsWithB) return -1;
                                if (!startsWithA && startsWithB) return 1;

                                // Se entrambi iniziano con la query o nessuno lo fa, ordina per lunghezza (più corto è meglio)
                                return a.address.freeformAddress.length - b.address.freeformAddress.length;
                            });

                        suggestionsList.innerHTML = ''; // Pulisce i suggerimenti precedenti

                        if (suggestionsData.length > 0) {
                            suggestionsData.forEach((result, index) => {
                                const suggestionItem = document.createElement('div');
                                suggestionItem.classList.add('suggestion-item');
                                suggestionItem.innerHTML = `<strong>${result.address.freeformAddress}</strong>`;
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
                suggestionsData = []; // Resetta i suggerimenti
            }
        });
    }

    // Gestisci la navigazione con tastiera e selezione con Enter
    addressInput.addEventListener('keydown', function (event) {
        const suggestionItems = suggestionsList.getElementsByClassName('suggestion-item');

        if (event.key === 'Enter') {
            // Mostra tutti i risultati se il campo di input è vuoto
            if (addressInput.value.trim() === '') {
                fetchAllProperties();
            } else if (suggestionItems.length > 0 && activeIndex >= 0) {
                // Seleziona il suggerimento attivo con Enter
                selectSuggestion(suggestionsData[activeIndex]);
            }
            event.preventDefault();
        } else if (event.key === 'ArrowDown') {
            // Naviga in basso nei suggerimenti
            activeIndex = (activeIndex + 1) % suggestionItems.length;
            setActiveSuggestion(suggestionItems, activeIndex);
            event.preventDefault();
        } else if (event.key === 'ArrowUp') {
            // Naviga in alto nei suggerimenti
            activeIndex = (activeIndex - 1 + suggestionItems.length) % suggestionItems.length;
            setActiveSuggestion(suggestionItems, activeIndex);
            event.preventDefault();
        }
    });

    // Funzione per evidenziare il suggerimento attivo
    function setActiveSuggestion(suggestionItems, index) {
        Array.from(suggestionItems).forEach((item, i) => {
            if (i === index) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
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


// ----------------------------------------------


document.addEventListener('DOMContentLoaded', function () {
    const citySearch = document.getElementById('citySearch');
    const suggestionsList = document.getElementById('suggestions');
    const TOMTOM_API_KEY = 'N4TIi8FzWNZv1sUqEUsREdKHYaG6HhSU';

    let activeIndex = -1; // Indice del suggerimento attivo
    let suggestionsData = []; // Variabile per memorizzare i dati dei suggerimenti

    // Funzione per ottenere i suggerimenti
    citySearch.addEventListener('input', function () {
        const query = citySearch.value.trim();
        activeIndex = -1; // Resetta l'indice ogni volta che cambia l'input

        if (query.length > 1) {
            fetch(`https://api.tomtom.com/search/2/search/${encodeURIComponent(query)}.json?key=${TOMTOM_API_KEY}&countrySet=IT&typeahead=true&limit=5&entityType=Municipality`)
                .then(response => response.json())
                .then(data => {
                    // Ordina i suggerimenti per pertinenza
                    suggestionsData = data.results
                        .sort((a, b) => {
                            // Priorità alle corrispondenze che iniziano con la query
                            const startsWithA = a.address.freeformAddress.toLowerCase().startsWith(query.toLowerCase());
                            const startsWithB = b.address.freeformAddress.toLowerCase().startsWith(query.toLowerCase());
                            if (startsWithA && !startsWithB) return -1;
                            if (!startsWithA && startsWithB) return 1;

                            // Se entrambi iniziano con la query o nessuno lo fa, ordina per lunghezza (più corto è meglio)
                            return a.address.freeformAddress.length - b.address.freeformAddress.length;
                        });

                    suggestionsList.innerHTML = ''; // Pulisce i suggerimenti precedenti

                    if (suggestionsData.length > 0) {
                        suggestionsData.forEach((result, index) => {
                            const suggestionItem = document.createElement('div');
                            suggestionItem.classList.add('suggestion-item');
                            suggestionItem.innerText = result.address.freeformAddress;

                            // Aggiungi l'evento di click per la selezione
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
            suggestionsData = []; // Resetta i suggerimenti
        }
    });

    // Navigazione con tastiera e selezione con Enter
    citySearch.addEventListener('keydown', function (event) {
        const suggestionItems = suggestionsList.getElementsByClassName('suggestion-item');

        if (event.key === 'Enter') {
            // Mostra tutti i risultati se il campo di input è vuoto
            if (citySearch.value.trim() === '') {
                fetchAllProperties();
            } else if (suggestionItems.length > 0) {
                // Se l'input non è vuoto, esegui la logica di selezione
                if (activeIndex >= 0 && suggestionsData[activeIndex]) {
                    selectSuggestion(suggestionsData[activeIndex]); // Usa il suggerimento attivo
                }
            }
            event.preventDefault();
        } else if (suggestionItems.length > 0) {
            if (event.key === 'ArrowDown') {
                // Muoviti in basso
                activeIndex = (activeIndex + 1) % suggestionItems.length;
                setActiveSuggestion(suggestionItems, activeIndex);
                event.preventDefault();
            } else if (event.key === 'ArrowUp') {
                // Muoviti in alto
                activeIndex = (activeIndex - 1 + suggestionItems.length) % suggestionItems.length;
                setActiveSuggestion(suggestionItems, activeIndex);
                event.preventDefault();
            }
        }
    });

    // Funzione per evidenziare il suggerimento attivo
    function setActiveSuggestion(suggestionItems, index) {
        Array.from(suggestionItems).forEach((item, i) => {
            if (i === index) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    // Funzione per gestire la selezione del suggerimento
    function selectSuggestion(result) {
        const latitude = result.position.lat;
        const longitude = result.position.lon;

        citySearch.value = result.address.freeformAddress;
        suggestionsList.innerHTML = '';

        // Reindirizza alla rotta index con le coordinate come parametri
        window.location.href = `/properties?latitude=${latitude}&longitude=${longitude}`;
    }

    // Funzione per mostrare tutte le proprietà quando l'input è vuoto
    function fetchAllProperties() {
        window.location.href = `/properties`; // Reindirizza alla pagina con tutti i risultati
    }
});
