<?php

namespace App\Http\Requests\API\Offer;

use App\Http\Requests\AbstractApiRequest;
use App\Models\Offer;
use Illuminate\Contracts\Validation\ValidationRule;

class DestroyOfferRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $offerId = $this->route('offer');
        $offer = Offer::find($offerId);

        if (!$offer) {
            return false;
        }

        return $offer->user_id == auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //'offer' => ['required', 'exists:offers,id']
        ];
    }
}
