<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'unique:properties,title', 'max:50'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'description' => ['nullable', 'string', 'max:300'],
            'num_rooms' => ['required', 'integer', 'min:1', 'max:50'],
            'num_beds' => ['required', 'integer', 'min:1', 'max:20'],
            'num_baths' => ['required', 'integer', 'min:0', 'max:5'],
            'mq' => ['required', 'integer', 'min:10', 'max:5000'],
            'zip' => ['required', 'numeric', 'digits:5'],
            'city' => ['required', 'string', 'min:2', 'max:50'],
            'address' => ['required', 'string', 'min:2', 'max:100'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'long' => ['required', 'numeric', 'between:-180,180'],
            'price' => ['required', 'numeric', 'min:10', 'max:999999.99'],
            'type' => ['required', 'string', 'in:mansion,ski-in/out,tree-house,apartment,dome,cave,cabin,lake,beach,castle'],
            'floor' => ['required', 'integer'],
            'available' => ['required', 'boolean'],
            'sponsored' => ['required', 'boolean'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'sponsors' => ['array', 'integer', 'exists:sponsors,id'],
            'services' => ['array', 'integer', 'exists:services,id'],
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'Il titolo è obbligatorio.',
            'title.string' => 'Il titolo deve essere una stringa.',
            'title.unique' => 'Questo titolo è già stato utilizzato per un’altra proprietà.',
            'title.max' => 'Il titolo non può superare i 50 caratteri.',

            'cover_image.image' => 'Il file caricato deve essere un\'immagine.',
            'cover_image.max' => 'L\'immagine di copertina non può superare i 4MB.',

            'description.string' => 'La descrizione deve essere una stringa.',
            'description.max' => 'La descrizione non può superare i 300 caratteri.',

            'num_rooms.required' => 'Il numero di stanze è obbligatorio.',
            'num_rooms.integer' => 'Il numero di stanze deve essere un numero intero.',
            'num_rooms.min' => 'Il numero di stanze deve essere almeno 1.',
            'num_rooms.max' => 'Il numero di stanze non può superare 50.',

            'num_beds.required' => 'Il numero di letti è obbligatorio.',
            'num_beds.integer' => 'Il numero di letti deve essere un numero intero.',
            'num_beds.min' => 'Il numero di letti deve essere almeno 1.',
            'num_beds.max' => 'Il numero di letti non può superare 20.',

            'num_baths.required' => 'Il numero di bagni è obbligatorio.',
            'num_baths.integer' => 'Il numero di bagni deve essere un numero intero.',
            'num_baths.min' => 'Il numero di bagni deve essere almeno 0.',
            'num_baths.max' => 'Il numero di bagni non può superare 5.',

            'mq.required' => 'I metri quadrati sono obbligatori.',
            'mq.integer' => 'I metri quadrati devono essere un numero intero.',
            'mq.min' => 'I metri quadrati devono essere almeno 10.',
            'mq.max' => 'I metri quadrati non possono superare 5000.',

            'zip.required' => 'Il CAP è obbligatorio.',
            'zip.digits' => 'Il CAP deve essere composto da 5 cifre.',

            'city.required' => 'La città è obbligatoria.',
            'city.string' => 'La città deve essere una stringa.',
            'city.min' => 'La città deve avere almeno 2 caratteri.',
            'city.max' => 'La città non può superare i 50 caratteri.',

            'address.required' => 'L\'indirizzo è obbligatorio.',
            'address.string' => 'L\'indirizzo deve essere una stringa.',
            'address.min' => 'L\'indirizzo deve avere almeno 2 caratteri.',
            'address.max' => 'L\'indirizzo non può superare i 100 caratteri.',

            'lat.required' => 'La latitudine è obbligatoria.',
            'lat.numeric' => 'La latitudine deve essere un valore numerico.',
            'lat.between' => 'La latitudine deve essere compresa tra -90 e 90.',

            'long.required' => 'La longitudine è obbligatoria.',
            'long.numeric' => 'La longitudine deve essere un valore numerico.',
            'long.between' => 'La longitudine deve essere compresa tra -180 e 180.',

            'price.required' => 'Il prezzo è obbligatorio.',
            'price.numeric' => 'Il prezzo deve essere un numero.',
            'price.min' => 'Il prezzo deve essere almeno 10.',
            'price.max' => 'Il prezzo non può superare 999,999.99.',

            'type.required' => 'Il tipo di proprietà è obbligatorio.',
            'type.string' => 'Il tipo di proprietà deve essere una stringa.',
            'type.in' => 'Il tipo di proprietà selezionato non è valido.',

            'floor.required' => 'Il piano è obbligatorio.',
            'floor.integer' => 'Il piano deve essere un numero intero.',

            'available.required' => 'La disponibilità è obbligatoria.',
            'available.boolean' => 'La disponibilità deve essere vera o falsa.',

            'sponsored.required' => 'Il campo sponsorizzato è obbligatorio.',
            'sponsored.boolean' => 'Il campo sponsorizzato deve essere vero o falso.',

            'user_id.required' => 'L\'ID utente è obbligatorio.',
            'user_id.integer' => 'L\'ID utente deve essere un numero intero.',
            'user_id.exists' => 'L\'ID utente specificato non esiste nel database.',

            'sponsors.array' => 'Il campo sponsors deve essere un array.',
            'sponsors.integer' => 'Ogni elemento di sponsors deve essere un numero intero.',
            'sponsors.exists' => 'Uno o più sponsor selezionati non esistono nel database.',

            'services.array' => 'Il campo services deve essere un array.',
            'services.integer' => 'Ogni elemento di services deve essere un numero intero.',
            'services.exists' => 'Uno o più servizi selezionati non esistono nel database.',
        ];
    }
}
