<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\OrderResource;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /**
     * List authenticated customer's orders, 10 per page with eager loading.
     * Question 4(b)
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('services')
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    /**
     * View one order with authorization check to prevent IDOR (Problem M2 / Fix B).
     * Question 3(a), Question 6(a)
     */
    public function show(Request $request, Order $order)
    {
        // Fix B: Add an OrderPolicy and call Gate::authorize('view', $order)
        Gate::authorize('view', $order);

        $order->load(['services', 'branch']);

        return new OrderResource($order);
    }

    /**
     * Customer books a pickup.
     * Returns 201 Created.
     * Question 3(a), Question 4(a)
     */
    public function storeBooking(StoreBookingRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        // Find default branch (or specified branch, or Dasmariñas as default)
        $branchId = $validated['branch_id'] ?? $user->branch_id ?? Branch::first()->id;

        $service = Service::findOrFail($validated['service_id']);
        $kg = (float) $validated['estimated_kg'];

        // Calculate pricing (service price * kg + P50 pickup fee)
        $pickupFee = 50.00;
        $serviceSubtotal = $service->price * $kg;
        $total = $serviceSubtotal + $pickupFee;

        $order = Order::create([
            'customer_id' => $user->id,
            'branch_id' => $branchId,
            'status' => 'received',
            'total' => $total,
            'pickup_address' => $validated['pickup_address'],
            'pickup_date' => $validated['pickup_date'],
            'internal_notes' => 'Booked via mobile app. Estimated weight: ' . $kg . ' kg.',
        ]);

        // Attach selected service to order
        $order->services()->attach($service->id, [
            'quantity' => $kg,
            'subtotal' => $serviceSubtotal,
        ]);

        return (new OrderResource($order->load('services')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Staff updates an order's status.
     * Question 3(a), Question 3(b) OrderPolicy::updateStatus
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        // Question 3(b): OrderPolicy::updateStatus
        Gate::authorize('updateStatus', $order);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:received,washing,ready,released'],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => new OrderResource($order),
        ], 200);
    }
}
