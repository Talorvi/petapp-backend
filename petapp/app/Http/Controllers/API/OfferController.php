<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Offer\DestroyOfferRequest;
use App\Http\Requests\API\Offer\OfferSearchRequest;
use App\Http\Requests\API\Offer\StoreOfferRequest;
use App\Http\Requests\API\Offer\UpdateOfferRequest;
use App\Http\Requests\API\Rating\DeleteRatingRequest;
use App\Http\Requests\API\Rating\StoreRatingRequest;
use App\Http\Requests\API\Rating\UpdateRatingRequest;
use App\Models\Offer;
use App\Models\Rating;
use App\Services\OfferService;
use DateTime;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OfferController extends Controller
{
    use HasUuids;

    private OfferService $offerService;

    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }

    public function index(OfferSearchRequest $request): JsonResponse
    {
        if($request->filled('query')) {
            $query = Offer::search($request->input('query'));
        } else {
            $query = Offer::query();
        }

        // Additional search criteria
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('minimum_rating')) {
            $query->where('average_rating', '>=', $request->input('minimum_rating'));
        }

        // Sorting
        if ($request->filled('sort_by')) {
            $direction = $request->input('sort_direction', 'desc');
            // Ensure the direction is either 'asc' or 'desc'
            $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

            $query->orderBy($request->input('sort_by'), $direction);
        } else {
            $direction = $request->input('sort_direction', 'desc');
            $query->orderBy('updated_at', $direction);
        }

        //dd($query->get());

        $offers = $query->paginate(10);

        return response()->json($offers);
    }

    public function show(string $offerId): JsonResponse
    {
        $offer = Offer::find($offerId);

        if (!$offer) {
            return response()->json(['message' => __('messages.offer_not_found')], ResponseAlias::HTTP_NOT_FOUND);
        }

        return response()->json($offer->load(['user', 'ratings']));
    }

    public function store(StoreOfferRequest $request): JsonResponse
    {
        $offer = $this->offerService->store($request->validated());
        return response()->json($offer, ResponseAlias::HTTP_CREATED);
    }

    public function update(UpdateOfferRequest $request, Offer $offer): JsonResponse
    {
        $this->offerService->update($request->validated(), $offer);
        return response()->json($offer);
    }

    public function destroy(DestroyOfferRequest $request): JsonResponse
    {
        $offer = Offer::with(['user', 'ratings'])->find($request->offer);

        if (!$offer) {
            return response()->json(['message' => __('messages.offer_not_found')], ResponseAlias::HTTP_NOT_FOUND);
        }

        $deleted = $offer->delete();

        if ($deleted) {
            return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
        } else {
            return response()->json(['message' =>  __('messages.could_not_delete_offer')], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeRating(StoreRatingRequest $request, Offer $offer): JsonResponse
    {
        // Check if the user has already rated this offer
        $existingRating = $offer->ratings()->where('user_id', auth()->id())->first();
        if ($existingRating) {
            return response()->json(['message' => __('messages.offer_already_rated')], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rating = $offer->ratings()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json($rating, 201);
    }

    public function getRatings($offerId): JsonResponse
    {
        $offer = Offer::with('ratings.user')->findOrFail($offerId);
        return response()->json($offer->ratings);
    }

    public function updateRating(UpdateRatingRequest $request, Rating $rating): JsonResponse
    {
        $rating->update($request->validated());

        return response()->json($rating, ResponseAlias::HTTP_OK);
    }


    public function destroyRating(DeleteRatingRequest $request, Rating $rating): JsonResponse
    {
        $rating->delete();

        return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
    }
}
