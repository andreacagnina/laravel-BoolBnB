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
            'address' => ['required', 'string', 'min:2', 'max:100'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'long' => ['nullable', 'numeric', 'between:-180,180'],
            'price' => ['required', 'numeric', 'min:10', 'max:999999.99'],
            'type' => ['required', 'string', 'in:mansion,ski-in/out,tree-house,apartment,dome,cave,cabin,lake,beach,castle'],
            'floor' => ['required', 'integer'],
            'available' => ['required', 'boolean'],
            'sponsors' => ['array',  'exists:sponsors,id'],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['exists:services,id'],
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a string.',
            'title.unique' => 'This title has already been used for another property.',
            'title.max' => 'The title cannot exceed 50 characters.',

            'cover_image.image' => 'The uploaded file must be an image.',
            'cover_image.max' => 'The cover image cannot exceed 4MB.',

            'description.string' => 'The description must be a string.',
            'description.max' => 'The description cannot exceed 300 characters.',

            'num_rooms.required' => 'The number of rooms is required.',
            'num_rooms.integer' => 'The number of rooms must be an integer.',
            'num_rooms.min' => 'The number of rooms must be at least 1.',
            'num_rooms.max' => 'The number of rooms cannot exceed 50.',

            'num_beds.required' => 'The number of beds is required.',
            'num_beds.integer' => 'The number of beds must be an integer.',
            'num_beds.min' => 'The number of beds must be at least 1.',
            'num_beds.max' => 'The number of beds cannot exceed 20.',

            'num_baths.required' => 'The number of baths is required.',
            'num_baths.integer' => 'The number of baths must be an integer.',
            'num_baths.min' => 'The number of baths must be at least 0.',
            'num_baths.max' => 'The number of baths cannot exceed 5.',

            'mq.required' => 'The square meters are required.',
            'mq.integer' => 'The square meters must be an integer.',
            'mq.min' => 'The square meters must be at least 10.',
            'mq.max' => 'The square meters cannot exceed 5000.',

            'address.required' => 'The address is required.',
            'address.string' => 'The address must be a string.',
            'address.min' => 'The address must be at least 2 characters long.',
            'address.max' => 'The address cannot exceed 100 characters.',

            'lat.required' => 'Latitude is required.',
            'lat.numeric' => 'Latitude must be a numeric value.',
            'lat.between' => 'Latitude must be between -90 and 90.',

            'long.required' => 'Longitude is required.',
            'long.numeric' => 'Longitude must be a numeric value.',
            'long.between' => 'Longitude must be between -180 and 180.',

            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 10.',
            'price.max' => 'Price cannot exceed 999,999.99.',

            'type.required' => 'Property type is required.',
            'type.string' => 'Property type must be a string.',
            'type.in' => 'The selected property type is invalid.',

            'floor.required' => 'Floor is required.',
            'floor.integer' => 'Floor must be an integer.',

            'available.required' => 'Availability is required.',
            'available.boolean' => 'Availability must be true or false.',

            'sponsors.array' => 'The sponsors field must be an array.',
            'sponsors.integer' => 'Each element of sponsors must be an integer.',
            'sponsors.exists' => 'One or more selected sponsors do not exist in the database.',

            'services.required' => 'At least one service must be selected.',
            'services.array' => 'The services must be an array of selected options.',
            'services.min' => 'You must select at least one service.',
            'services.*.exists' => 'One or more selected services do not exist. Please select valid services.',
        ];
    }
}
