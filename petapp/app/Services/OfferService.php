<?php

namespace App\Services;

use App\Models\Offer;

class OfferService
{
    public function store(array $data): Offer
    {
        $offer = Offer::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'user_id' => auth()->id(),
            'price' => $data['price'] ?? null,
            'end_date' => $data['end_date'] ?? null,
        ]);

        return $offer;
    }

    public function update(array $data, Offer $offer): bool
    {
        return $offer->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price']?? null,
            'end_date' => $data['end_date']?? null
        ]);
    }
}
