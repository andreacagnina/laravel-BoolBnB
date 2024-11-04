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


// TOM TOM
// document.addEventListener('DOMContentLoaded', function () {
//     const addressInput = document.getElementById('address');
//     const suggestionsList = document.getElementById('suggestions');
//     const latInput = document.getElementById('lat'); // Campo nascosto per latitudine
//     const longInput = document.getElementById('long'); // Campo nascosto per longitudine

//     const TOMTOM_API_KEY = 'N4TIi8FzWNZv1sUqEUsREdKHYaG6HhSU'; // Inserisci qui la tua chiave API di TomTom

//     let currentIndex = -1; // Indice per il suggerimento attualmente selezionato
//     let filteredResults = []; // Array globale per i risultati filtrati

//     addressInput.addEventListener('input', function () {
//         const query = addressInput.value;
//         currentIndex = -1; // Reset dell’indice ogni volta che si digita

//         if (query.length > 1) { // Invia la richiesta solo se l'utente ha digitato almeno 2 caratteri
//             fetch(`https://api.tomtom.com/search/2/search/${encodeURIComponent(query)}.json?key=${TOMTOM_API_KEY}&countrySet=IT`)
//                 .then(response => response.json())
//                 .then(data => {
//                     suggestionsList.innerHTML = ''; // Pulisce i suggerimenti precedenti
//                     filteredResults = []; // Reset dei risultati filtrati

//                     if (data.results && data.results.length > 0) {
//                         // Filtra i risultati per mostrare solo quelli con CAP (postalCode)
//                         filteredResults = data.results.filter(result => result.address.postalCode);

//                         filteredResults.forEach((result, index) => {
//                             const fullAddress = result.address.freeformAddress;

//                             // Crea un div per ogni suggerimento con indirizzo completo
//                             const suggestionItem = document.createElement('div');
//                             suggestionItem.classList.add('suggestion-item');
//                             suggestionItem.innerHTML = `<strong>${fullAddress}</strong>`;
//                             suggestionItem.setAttribute('data-index', index); // Imposta un attributo indice

//                             // Click sul suggerimento
//                             suggestionItem.addEventListener('click', function () {
//                                 selectSuggestion(result);
//                             });
//                             suggestionsList.appendChild(suggestionItem);
//                         });
//                     }
//                 })
//                 .catch(error => console.error('Errore:', error));
//         } else {
//             suggestionsList.innerHTML = ''; // Pulisce i suggerimenti se ci sono meno di 2 caratteri
//         }
//     });

//     // Funzione per selezionare un suggerimento
//     function selectSuggestion(result) {
//         addressInput.value = result.address.freeformAddress;
//         latInput.value = result.position.lat;
//         longInput.value = result.position.lon;
//         suggestionsList.innerHTML = ''; // Pulisce la lista dei suggerimenti
//     }

//     // Gestione delle frecce della tastiera e del tasto Invio
//     addressInput.addEventListener('keydown', function (e) {
//         const items = document.querySelectorAll('.suggestion-item');

//         if (e.key === 'ArrowDown') {
//             e.preventDefault();
//             // Scorri verso il basso
//             currentIndex = (currentIndex + 1) % items.length;
//             updateActiveSuggestion(items);
//         } else if (e.key === 'ArrowUp') {
//             e.preventDefault();
//             // Scorri verso l'alto
//             currentIndex = (currentIndex - 1 + items.length) % items.length;
//             updateActiveSuggestion(items);
//         } else if (e.key === 'Enter' && currentIndex > -1) {
//             e.preventDefault();
//             // Seleziona il suggerimento attivo
//             const result = filteredResults[currentIndex];
//             selectSuggestion(result);
//         }
//     });

//     // Aggiorna la classe attiva per evidenziare il suggerimento selezionato
//     function updateActiveSuggestion(items) {
//         items.forEach((item, index) => {
//             item.classList.toggle('active', index === currentIndex);
//         });
//     }

//     // Nasconde i suggerimenti quando si fa clic fuori dall'input
//     document.addEventListener('click', function (e) {
//         if (!addressInput.contains(e.target) && !suggestionsList.contains(e.target)) {
//             suggestionsList.innerHTML = '';
//         }
//     });
// });
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
