<?php

namespace App\Http\Requests\API\Offer;

use App\Http\Requests\AbstractApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreOfferRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'nullable|numeric',
            'end_date' => 'nullable|date|after:tomorrow'
        ];
    }
}
