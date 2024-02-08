<?php

namespace App\Http\Requests\API\Offer;

use App\Http\Requests\AbstractApiRequest;
use App\Models\Offer;

class DeleteOfferImageRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Implement your authorization logic here.
        // You may want to ensure that the offer belongs to the user and that the image exists.
        $offer = Offer::find($this->route('offerId'));
        if (!$offer) {
            return false;
        }

        return $offer->user_id == auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // Rules can be defined here if needed, for example:
            'imageId' => 'required|exists:media,id'
        ];
    }
}
