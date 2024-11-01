<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSponsorRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:20', Rule::unique('sponsors')->ignore($this->sponsor)],
            'price' => ['required', 'numeric', 'min:2.99', 'max:99.99'],
            'duration' => ['required', 'integer', 'min:24', 'max:730'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Il nome è obbligatorio.',
            'name.string' => 'Il nome deve essere una stringa.',
            'name.max' => 'Il nome non può superare i 20 caratteri.',
            'name.unique' => 'Questo nome è già utilizzato per un altro sponsor.',

            'price.required' => 'Il prezzo è obbligatorio.',
            'price.numeric' => 'Il prezzo deve essere un numero.',
            'price.min' => 'Il prezzo minimo è 2.99 €.',
            'price.max' => 'Il prezzo massimo è 99.99 €.',

            'duration.required' => 'La durata è obbligatoria.',
            'duration.integer' => 'La durata deve essere un numero intero.',
            'duration.min' => 'La durata minima è di 24 ore.',
            'duration.max' => 'La durata massima è di 730 ore.',
        ];
    }
}
