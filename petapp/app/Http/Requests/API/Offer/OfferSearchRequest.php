<?php

namespace App\Http\Requests\API\Offer;

use Illuminate\Foundation\Http\FormRequest;

class OfferSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        // return false if you want to add authorization logic
        return true;
    }

    public function rules(): array
    {
        return [
            'query' => 'string|nullable',
            'user_id' => 'uuid|nullable',
            'minimum_rating' => 'numeric|nullable',
            'sort_by' => 'in:price,average_rating,updated_at|nullable',
            'sort_direction' => 'in:asc,desc|nullable',
        ];
    }
}
