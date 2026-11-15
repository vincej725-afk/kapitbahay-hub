<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KapitbahayHubApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $staffImus;
    protected User $staffBacoor;
    protected User $customerA;
    protected User $customerB;
    protected Branch $branchImus;
    protected Branch $branchBacoor;
    protected Service $serviceWdf;
    protected Order $orderBacoor;
    protected Order $orderCustomerB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->owner = User::where('email', 'liza.santos@kapitbahay.ph')->first();
        $this->staffImus = User::where('email', 'staff.imus@kapitbahay.ph')->first();
        $this->staffBacoor = User::where('email', 'staff.bacoor@kapitbahay.ph')->first();
        $this->customerA = User::where('email', 'customer.a@gmail.com')->first();
        $this->customerB = User::where('email', 'customer.b@gmail.com')->first();

        $this->branchImus = Branch::where('name', 'Imus Branch')->first();
        $this->branchBacoor = Branch::where('name', 'Bacoor Branch')->first();
        $this->serviceWdf = Service::where('name', 'Wash-Dry-Fold')->first();

        $this->orderCustomerB = Order::where('customer_id', $this->customerB->id)->first();
        $this->orderBacoor = $this->orderCustomerB;
    }

    /**
     * Question 5 Example: Log in with the correct password returns 200 with a token.
     */
    public function test_example_login_with_correct_password_returns_200_with_token(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'customer.a@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => ['id', 'name', 'email', 'role'],
            ]);
    }

    /**
     * Question 5(a) TC1: Book a pickup for 3 kg of Wash-Dry-Fold returns 422 (min 5 kg).
     */
    public function test_tc1_book_pickup_under_minimum_kg_returns_422(): void
    {
        $token = $this->customerA->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/bookings', [
                'service_id' => $this->serviceWdf->id,
                'estimated_kg' => 3, // Below min 5 kg rule
                'pickup_address' => 'Nueno Avenue, Imus, Cavite',
                'pickup_date' => now()->toDateString(),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['estimated_kg']);
    }

    /**
     * Question 5(a) TC2: Staff from Imus updates a Bacoor order to "ready" returns 403 Forbidden.
     */
    public function test_tc2_staff_from_imus_cannot_update_bacoor_order(): void
    {
        $token = $this->staffImus->createToken('staff-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/v1/orders/' . $this->orderBacoor->id . '/status', [
                'status' => 'ready',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Question 5(a) TC3: Customer A requests Customer B's order returns 403 Forbidden (Prevents IDOR M2).
     */
    public function test_tc3_customer_a_requesting_customer_b_order_returns_403_forbidden(): void
    {
        $token = $this->customerA->createToken('customer-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/orders/' . $this->orderCustomerB->id);

        $response->assertStatus(403);
    }

    /**
     * Question 5(b) Custom Test Case: Pickup date yesterday returns 422 Unprocessable Content.
     */
    public function test_custom_booking_with_past_date_returns_422(): void
    {
        $token = $this->customerA->createToken('customer-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/bookings', [
                'service_id' => $this->serviceWdf->id,
                'estimated_kg' => 6,
                'pickup_address' => 'Nueno Ave, Imus',
                'pickup_date' => now()->subDay()->toDateString(), // Past date
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['pickup_date']);
    }

    /**
     * Question 4(a) #1: Successful booking returns 201 Created.
     */
    public function test_customer_books_pickup_successfully_returns_201(): void
    {
        $token = $this->customerA->createToken('customer-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/bookings', [
                'service_id' => $this->serviceWdf->id,
                'estimated_kg' => 7,
                'pickup_address' => 'Imus, Cavite',
                'pickup_date' => now()->addDays(2)->toDateString(),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.status', 'received')
            ->assertJsonPath('data.total', 295); // 7 * 35 + 50
    }

    /**
     * Question 4(c): Output JSON does not expose internal_notes or customer_id.
     */
    public function test_order_json_does_not_contain_internal_notes_or_customer_id(): void
    {
        $token = $this->customerA->createToken('customer-token')->plainTextToken;
        $orderA = Order::where('customer_id', $this->customerA->id)->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/orders/' . $orderA->id);

        $response->assertStatus(200)
            ->assertJsonMissing(['internal_notes' => $orderA->internal_notes])
            ->assertJsonMissing(['customer_id' => $this->customerA->id]);
    }

    /**
     * Question 4(a) #2: Request without token returns 401 Unauthorized.
     */
    public function test_unauthenticated_request_returns_401(): void
    {
        $response = $this->getJson('/api/v1/orders');
        $response->assertStatus(401);
    }

    /**
     * Question 4(a) #4: Customer asks for order number 99999 which does not exist returns 404.
     */
    public function test_non_existent_order_returns_404(): void
    {
        $token = $this->customerA->createToken('customer-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/orders/99999');

        $response->assertStatus(404);
    }

    /**
     * Question 4(b): Orders index returns paginated list of authenticated customer's orders.
     */
    public function test_customer_lists_orders_paginated(): void
    {
        $token = $this->customerA->createToken('customer-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'branch_id', 'status', 'total', 'pickup_address', 'pickup_date', 'created_at']
                ],
                'links',
                'meta' => ['current_page', 'per_page'],
            ]);
    }

    /**
     * Question 6(a) M3: Login endpoint is rate limited to 5 attempts per minute.
     */
    public function test_login_throttling_after_5_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/login', [
                'email' => 'wrong@kapitbahay.ph',
                'password' => 'wrongpass',
            ]);
        }

        // 6th attempt should be throttled with 429 Too Many Requests
        $response = $this->postJson('/api/v1/login', [
            'email' => 'wrong@kapitbahay.ph',
            'password' => 'wrongpass',
        ]);

        $response->assertStatus(429);
    }

    /**
     * Owner can update status of orders in any branch.
     */
    public function test_owner_can_update_status_of_any_branch(): void
    {
        $token = $this->owner->createToken('owner-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patchJson('/api/v1/orders/' . $this->orderBacoor->id . '/status', [
                'status' => 'washing',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('washing', $this->orderBacoor->fresh()->status);
    }
}
