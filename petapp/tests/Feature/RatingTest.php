<?php

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Str;
use Tests\TestCase;
use Tests\Utilities\DisablesSearchSyncing;

class RatingTest extends TestCase
{
    use DatabaseTransactions;
    use DisablesSearchSyncing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpScoutSyncing();
    }

    protected function tearDown(): void
    {
        $this->tearDownScoutSyncing();
        parent::tearDown();
    }

    public function test_user_can_rate_an_offer()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create();
        $offer->save();

        $this->actingAs($user);

        $ratingData = [
            'rating' => 5,
            'review' => 'Great offer!',
        ];

        $response = $this->postJson("/api/offers/{$offer->id->toString()}/ratings", $ratingData);

        $response->assertStatus(201)
            ->assertJsonFragment($ratingData);
    }

    public function test_user_can_update_their_rating()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create();
        $offer->save();
        $rating = $offer->ratings()->create([
            'user_id' => $user->id->toString(),
            'rating' => 4,
            'review' => 'Good offer',
        ]);
        $rating->save();

        $this->actingAs($user);

        $updatedRatingData = [
            'rating' => 5,
            'review' => 'Excellent offer!',
        ];

        $response = $this->putJson("/api/ratings/{$rating->id->toString()}", $updatedRatingData);

        $response->assertOk()
            ->assertJsonFragment($updatedRatingData);
    }

    public function test_user_can_view_ratings_for_an_offer()
    {
        $offer = Offer::factory()->create();
        $offer->save();
        $rating = $offer->ratings()->create([
            'user_id' => User::factory()->create()->id->toString(),
            'rating' => 4,
            'review' => 'Good offer',
        ]);
        $rating->save();

        $response = $this->getJson("/api/offers/{$offer->id->toString()}/ratings");

        $response->assertOk()
            ->assertJsonStructure([
                '*' => ['id', 'user_id', 'rating', 'review']
            ]);
    }

    public function test_user_can_delete_their_rating()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create();
        $offer->save();
        $rating = $offer->ratings()->create([
            'user_id' => $user->id->toString(),
            'rating' => 4,
            'review' => 'Good offer',
        ]);
        $rating->save();

        $this->actingAs($user);

        $response = $this->deleteJson("/api/ratings/{$rating->id->toString()}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('ratings', ['id' => $rating->id->toString()]);
    }

    public function test_unauthorized_user_cannot_rate_an_offer()
    {
        $offer = Offer::factory()->create();
        $offer->save();

        $ratingData = [
            'user_id' => auth()->id(),
            'rating' => 3,
            'review' => 'Average offer',
        ];

        $response = $this->postJson("/api/offers/{$offer->id->toString()}/ratings", $ratingData);

        $response->assertStatus(401);
    }

    public function test_user_cannot_rate_an_offer_twice()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create();
        $offer->save();

        $this->actingAs($user);

        $ratingData = [
            'rating' => 4,
            'review' => 'Good offer',
        ];

        $this->postJson("/api/offers/{$offer->id->toString()}/ratings", $ratingData);

        $secondResponse = $this->postJson("/api/offers/{$offer->id->toString()}/ratings", $ratingData);
        $secondResponse->assertStatus(422);
    }

    public function test_unauthorized_user_cannot_update_rating()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->create();
        $offer->save();
        $rating = $offer->ratings()->create([
            'user_id' => $otherUser->id->toString(),
            'rating' => 4,
            'review' => 'Good offer',
        ]);
        $rating->save();

        $this->actingAs($user);

        $updatedRatingData = [
            'rating' => 5,
            'review' => 'Excellent offer!',
        ];

        $response = $this->putJson("/api/ratings/{$rating->id->toString()}", $updatedRatingData);
        $response->assertStatus(403);
    }

    public function test_user_cannot_create_rating_with_invalid_data()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create();

        $this->actingAs($user);

        $invalidRatingData = [
            'rating' => 6, // Invalid rating
            'review' => str_repeat('A', 300), // Too long review
        ];

        $response = $this->postJson("/api/offers/{$offer->id->toString()}/ratings", $invalidRatingData);
        $response->assertStatus(422);
    }

    public function test_user_cannot_rate_non_existent_offer()
    {
        $user = User::factory()->create();
        $nonExistentOfferId = Str::uuid();

        $this->actingAs($user);

        $ratingData = [
            'rating' => 4,
            'review' => 'Good offer',
        ];

        $response = $this->postJson("/api/offers/{$nonExistentOfferId}/ratings", $ratingData);
        $response->assertStatus(404);
    }

    public function test_user_cannot_update_non_existent_rating()
    {
        $user = User::factory()->create();
        $nonExistentRatingId = Str::uuid();

        $this->actingAs($user);

        $updatedRatingData = [
            'rating' => 5,
            'review' => 'Excellent offer!',
        ];

        $response = $this->putJson("/api/ratings/{$nonExistentRatingId}", $updatedRatingData);
        $response->assertStatus(404);
    }

    public function test_user_cannot_delete_non_existent_rating()
    {
        $user = User::factory()->create();
        $nonExistentRatingId = Str::uuid();

        $this->actingAs($user);

        $response = $this->deleteJson("/api/ratings/{$nonExistentRatingId}");
        $response->assertStatus(404);
    }

    public function test_user_cannot_view_ratings_for_non_existent_offer()
    {
        $nonExistentOfferId = Str::uuid();

        $response = $this->getJson("/api/offers/{$nonExistentOfferId}/ratings");
        $response->assertStatus(404);
    }

    public function test_user_cannot_update_others_rating()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->create();

        // Other user's rating
        $rating = $offer->ratings()->create([
            'user_id' => $otherUser->id->toString(),
            'rating' => 3,
            'review' => 'Average offer',
        ]);

        $this->actingAs($user);

        $updatedRatingData = [
            'rating' => 5,
            'review' => 'Actually, it\'s great!',
        ];

        $response = $this->putJson("/api/ratings/{$rating->id->toString()}", $updatedRatingData);
        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_others_rating()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->create();

        // Other user's rating
        $rating = $offer->ratings()->create([
            'user_id' => $otherUser->id->toString(),
            'rating' => 3,
            'review' => 'Average offer',
        ]);

        $this->actingAs($user);

        $response = $this->deleteJson("/api/ratings/{$rating->id->toString()}");
        $response->assertStatus(403);
    }

}
