<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KapitbahayHubWebTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $staffImus;
    protected User $staffBacoor;
    protected User $customer;
    protected Branch $branchImus;
    protected Branch $branchBacoor;
    protected Service $serviceWdf;
    protected Order $orderImus;
    protected Order $orderBacoor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->owner = User::where('email', 'liza.santos@kapitbahay.ph')->first();
        $this->staffImus = User::where('email', 'staff.imus@kapitbahay.ph')->first();
        $this->staffBacoor = User::where('email', 'staff.bacoor@kapitbahay.ph')->first();
        $this->customer = User::where('email', 'customer.a@gmail.com')->first();

        $this->branchImus = Branch::where('name', 'Imus Branch')->first();
        $this->branchBacoor = Branch::where('name', 'Bacoor Branch')->first();
        $this->serviceWdf = Service::where('name', 'Wash-Dry-Fold')->first();

        $this->orderImus = Order::where('branch_id', $this->branchImus->id)->first();
        $this->orderBacoor = Order::where('branch_id', $this->branchBacoor->id)->first();
    }

    /**
     * Test staff session login.
     */
    public function test_staff_can_login_via_web_session(): void
    {
        $response = $this->post('/login', [
            'email' => 'staff.imus@kapitbahay.ph',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('staff.orders.index'));
        $this->assertAuthenticatedAs($this->staffImus);
    }

    /**
     * Test staff can only see their own branch orders.
     */
    public function test_staff_only_sees_their_own_branch_orders(): void
    {
        $response = $this->actingAs($this->staffImus)->get(route('staff.orders.index'));

        $response->assertStatus(200);
        $response->assertSee($this->orderImus->customer->name);
        $response->assertDontSee($this->orderBacoor->customer->name);
    }

    /**
     * Test staff can update order status for their own branch.
     */
    public function test_staff_can_update_status_for_their_branch(): void
    {
        $response = $this->actingAs($this->staffImus)
            ->patch(route('staff.orders.status', $this->orderImus), [
                'status' => 'ready',
            ]);

        $response->assertRedirect();
        $this->assertEquals('ready', $this->orderImus->fresh()->status);
    }

    /**
     * Test staff cannot update order status for another branch.
     */
    public function test_staff_cannot_update_status_for_another_branch(): void
    {
        $response = $this->actingAs($this->staffImus)
            ->patch(route('staff.orders.status', $this->orderBacoor), [
                'status' => 'ready',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test staff can record walk-in order.
     */
    public function test_staff_can_record_walk_in_order(): void
    {
        $response = $this->actingAs($this->staffImus)
            ->post(route('staff.orders.store'), [
                'customer_name' => 'Walk-in Customer Test',
                'customer_phone' => '0999-888-7777',
                'service_id' => $this->serviceWdf->id,
                'quantity' => 6,
                'payment_method' => 'cash',
                'internal_notes' => 'Test walk-in order',
            ]);

        $response->assertRedirect(route('staff.orders.index'));
        $this->assertDatabaseHas('orders', [
            'branch_id' => $this->branchImus->id,
            'total' => 210.00, // 6kg * 35
        ]);
    }

    /**
     * Test owner can view orders across all branches.
     */
    public function test_owner_can_view_all_branches_dashboard(): void
    {
        $response = $this->actingAs($this->owner)->get(route('owner.dashboard'));

        $response->assertStatus(200);
        $response->assertSee($this->orderImus->customer->name);
        $response->assertSee($this->orderBacoor->customer->name);
    }

    /**
     * Test owner can update service prices.
     */
    public function test_owner_can_update_service_price(): void
    {
        $response = $this->actingAs($this->owner)
            ->patch(route('owner.services.update', $this->serviceWdf), [
                'price' => 40.00,
            ]);

        $response->assertRedirect(route('owner.services'));
        $this->assertEquals(40.00, $this->serviceWdf->fresh()->price);
    }

    /**
     * Test staff cannot access owner pricing page.
     */
    public function test_staff_cannot_access_owner_pricing_page(): void
    {
        $response = $this->actingAs($this->staffImus)
            ->get(route('owner.services'));

        $response->assertStatus(403);
    }
}
