<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with Kapitbahay Laundry Hub initial data.
     */
    public function run(): void
    {
        // 1. Seed Branches
        $dasma = Branch::create([
            'name' => 'Dasmariñas Branch',
            'address' => 'Aguinaldo Highway, Dasmariñas, Cavite',
            'phone' => '0917-111-0001',
        ]);

        $imus = Branch::create([
            'name' => 'Imus Branch',
            'address' => 'Nueno Avenue, Imus, Cavite',
            'phone' => '0917-111-0002',
        ]);

        $bacoor = Branch::create([
            'name' => 'Bacoor Branch',
            'address' => 'Tirona Highway, Bacoor, Cavite',
            'phone' => '0917-111-0003',
        ]);

        // 2. Seed Services and Prices
        $wdf = Service::create([
            'name' => 'Wash-Dry-Fold',
            'price' => 35.00,
            'unit' => 'kg',
            'min_quantity' => 5,
        ]);

        $wo = Service::create([
            'name' => 'Wash only',
            'price' => 25.00,
            'unit' => 'kg',
            'min_quantity' => 5,
        ]);

        $comforter = Service::create([
            'name' => 'Comforter',
            'price' => 180.00,
            'unit' => 'piece',
            'max_quantity' => 10,
        ]);

        $pickupDelivery = Service::create([
            'name' => 'Pickup and delivery',
            'price' => 50.00,
            'unit' => 'trip',
        ]);

        // 3. Seed Users (Owner, Branch Staff, and Customers)
        $password = Hash::make('password123');

        $owner = User::create([
            'name' => 'Mrs. Liza Santos',
            'email' => 'liza.santos@kapitbahay.ph',
            'password' => $password,
            'role' => 'owner',
            'phone' => '0917-000-0001',
            'address' => 'Cavite',
        ]);

        $staffDasma = User::create([
            'name' => 'Dasmariñas Staff',
            'email' => 'staff.dasma@kapitbahay.ph',
            'password' => $password,
            'role' => 'staff',
            'branch_id' => $dasma->id,
            'phone' => '0917-222-0001',
            'address' => 'Dasmariñas, Cavite',
        ]);

        $staffImus = User::create([
            'name' => 'Imus Staff',
            'email' => 'staff.imus@kapitbahay.ph',
            'password' => $password,
            'role' => 'staff',
            'branch_id' => $imus->id,
            'phone' => '0917-222-0002',
            'address' => 'Imus, Cavite',
        ]);

        $staffBacoor = User::create([
            'name' => 'Bacoor Staff',
            'email' => 'staff.bacoor@kapitbahay.ph',
            'password' => $password,
            'role' => 'staff',
            'branch_id' => $bacoor->id,
            'phone' => '0917-222-0003',
            'address' => 'Bacoor, Cavite',
        ]);

        $customerA = User::create([
            'name' => 'Customer A (Juan Dela Cruz)',
            'email' => 'customer.a@gmail.com',
            'password' => $password,
            'role' => 'customer',
            'phone' => '0918-123-4567',
            'address' => 'Block 5 Lot 2, Imus, Cavite',
        ]);

        $customerB = User::create([
            'name' => 'Customer B (Maria Clara)',
            'email' => 'customer.b@gmail.com',
            'password' => $password,
            'role' => 'customer',
            'phone' => '0918-765-4321',
            'address' => 'Phase 2, Bacoor, Cavite',
        ]);

        // 4. Seed Orders
        // Order 1: Customer A's order at Imus
        $order1 = Order::create([
            'customer_id' => $customerA->id,
            'branch_id' => $imus->id,
            'status' => 'washing',
            'total' => 260.00, // (6kg * 35) + 50
            'pickup_address' => $customerA->address,
            'pickup_date' => now()->toDateString(),
            'internal_notes' => 'Customer requested extra fabric softener.',
        ]);
        $order1->services()->attach($wdf->id, ['quantity' => 6, 'subtotal' => 210.00]);
        $order1->services()->attach($pickupDelivery->id, ['quantity' => 1, 'subtotal' => 50.00]);
        Payment::create(['order_id' => $order1->id, 'amount' => 260.00, 'status' => 'paid', 'payment_method' => 'cash']);

        // Order 2: Customer B's order at Bacoor (used for TC2 and TC3 tests)
        $order2 = Order::create([
            'customer_id' => $customerB->id,
            'branch_id' => $bacoor->id,
            'status' => 'received',
            'total' => 410.00, // 2 comforters (360) + 50
            'pickup_address' => $customerB->address,
            'pickup_date' => now()->addDay()->toDateString(),
            'internal_notes' => 'Handle delicate white comforter with care.',
        ]);
        $order2->services()->attach($comforter->id, ['quantity' => 2, 'subtotal' => 360.00]);
        $order2->services()->attach($pickupDelivery->id, ['quantity' => 1, 'subtotal' => 50.00]);
        Payment::create(['order_id' => $order2->id, 'amount' => 410.00, 'status' => 'pending', 'payment_method' => 'gcash']);
    }
}
