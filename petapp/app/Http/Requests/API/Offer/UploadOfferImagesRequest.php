<?php

namespace App\Http\Requests\API\Offer;

use App\Http\Requests\AbstractApiRequest;
use App\Models\Offer;

class UploadOfferImagesRequest extends AbstractApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $offer = $this->route('offer');

        $offer = Offer::find($offer);

        if (!$offer) {
            return false;
        }

        return $offer->user_id == (string)auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
        ];
    }
}
