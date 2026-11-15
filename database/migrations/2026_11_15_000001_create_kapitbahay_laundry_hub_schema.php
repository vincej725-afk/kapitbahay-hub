<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Branches table
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Dasmariñas, Imus, Bacoor
            $table->string('address');
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        // 2. Add role, branch_id, phone, address to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer'); // customer, staff, owner
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
        });

        // 3. Services table
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('price', 8, 2);
            $table->string('unit')->default('kg'); // kg, piece, trip
            $table->unsignedInteger('min_quantity')->nullable();
            $table->unsignedInteger('max_quantity')->nullable();
            $table->timestamps();
        });

        // 4. Orders table (id, customer_id, branch_id, status, total, pickup_address, internal_notes, created_at)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->enum('status', ['received', 'washing', 'ready', 'released'])->default('received');
            $table->decimal('total', 10, 2)->default(0.00);
            $table->string('pickup_address');
            $table->date('pickup_date')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });

        // 5. Order Services pivot table
        Schema::create('order_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->decimal('quantity', 8, 2)->default(1.00);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // 6. Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('paid'); // pending, paid
            $table->string('payment_method')->default('cash'); // cash, gcash
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_service');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('services');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['role', 'branch_id', 'phone', 'address']);
        });

        Schema::dropIfExists('branches');
    }
};
