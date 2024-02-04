<?php

namespace Tests\Feature;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Utilities\DisablesSearchSyncing;

class OfferTest extends TestCase
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

    public function test_user_can_create_offer()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $offerData = [
            'title' => 'Sample Offer',
            'description' => 'Sample Description',
            'price' => 100.00,
        ];

        $response = $this->postJson('/api/offers', $offerData);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Sample Offer',
                'description' => 'Sample Description',
                'price' => 100.00,
            ])
            ->assertJsonStructure([
                'id',
                'user_id',
                'title',
                'description',
                'price',
                'end_date',
                'created_at',
                'updated_at'
            ]);
    }

    public function test_user_can_update_their_offer()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $updatedData = [
            'id' => $offer->id,
            'title' => 'Updated Offer',
            'description' => 'Updated Description',
            'price' => 150.00,
            'user_id' => $user->id,
        ];

        $response = $this->putJson("/api/offers/{$offer->id}", $updatedData);

        $response->assertOk()
            ->assertJson([
                'title' => 'Updated Offer',
                'description' => 'Updated Description',
                'price' => 150.00,
            ]);
    }

    public function test_user_cant_update_other_users_offer()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $otherUser->id]);
        $this->actingAs($user);

        $updatedData = [
            'id' => $offer->id,
            'title' => 'Updated Offer',
            'description' => 'Updated Description',
            'price' => 150.00,
            'user_id' => $otherUser->id,
        ];

        $response = $this->putJson("/api/offers/{$offer->id}", $updatedData);

        $response->assertStatus(403);
    }

    public function test_user_can_view_an_offer()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $user->id]);
        $offer->save();

        $response = $this->getJson("/api/offers/{$offer->id}");

        $response->assertOk()
            ->assertJson([
                'id' => $offer->id->toString(),
                'title' => $offer->title,
                'description' => $offer->description,
                'price' => $offer->price,
            ]);
    }

    public function test_user_cant_view_a_non_existent_offer()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $user->id]);
        $offer->save();

        $response = $this->getJson(sprintf("/api/offers/%s", Str::uuid()));

        $response->assertNotFound();
    }

    public function test_user_can_view_all_offers()
    {
        $user = User::factory()->create();
        $offer1 = Offer::factory()->create(['user_id' => $user->id]);
        $offer1->save();

        $offer2 = Offer::factory()->create(['user_id' => $user->id]);
        $offer2->save();

        $response = $this->getJson("/api/offers");

        $response->assertOk();

        $response->assertJsonFragment([
            'id' => $offer1->id->toString(),
            'title' => $offer1->title,
            'description' => $offer1->description,
            'price' => number_format((float)$offer1->price, 2, '.', ''),
        ]);

        $response->assertJsonFragment([
            'id' => $offer2->id->toString(),
            'title' => $offer2->title,
            'description' => $offer2->description,
            'price' => number_format((float)$offer2->price, 2, '.', ''),
        ]);
    }

    public function test_user_can_delete_their_offer()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $user->id]);
        $offer->save();
        $this->actingAs($user);

        $response = $this->deleteJson("/api/offers/{$offer->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('offers', ['id' => $offer->id]);
    }

    public function test_user_cant_delete_non_existing_offer()
    {
        $user = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $user->id]);
        $offer->save();
        $this->actingAs($user);

        $response = $this->deleteJson(sprintf("/api/offers/%s", Str::uuid()));

        $response->assertForbidden();
    }

    public function test_user_cant_delete_other_users_offer()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offer = Offer::factory()->create(['user_id' => $otherUser->id]);
        $offer->save();
        $this->actingAs($user);

        $response = $this->deleteJson("/api/offers/{$offer->id}");

        $response->assertForbidden();
    }
}
