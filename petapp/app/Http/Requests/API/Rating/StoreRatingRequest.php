<?php

namespace App\Http\Requests\API\Rating;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $offer = $this->route('offer');

        if (!$offer) {
            return false;
        }

        return $offer->user_id !== auth()->id();
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'review' => [
                'string',
                'min:1',
                'max:256',
            ],
            'user_id' => [
                'uuid'
            ]
        ];
    }
}
