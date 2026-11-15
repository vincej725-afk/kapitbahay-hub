<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StaffOrderController extends Controller
{
    /**
     * Display orders for the staff member's assigned branch only.
     * Enforces: "Each branch's staff should handle only their own branch's orders."
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Enforce branch scope for staff; owner can select branch or see all
        $ordersQuery = Order::with(['customer', 'services', 'branch'])
            ->latest();

        if ($user->role === 'staff') {
            $ordersQuery->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $ordersQuery->where('branch_id', $request->input('branch_id'));
        }

        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->input('status'));
        }

        $orders = $ordersQuery->paginate(15)->withQueryString();

        return view('staff.orders.index', [
            'orders' => $orders,
            'user' => $user,
            'currentBranch' => $user->branch,
        ]);
    }

    /**
     * Show form to record a walk-in order.
     */
    public function create(): View
    {
        $services = Service::all();
        $user = Auth::user();

        return view('staff.orders.create', [
            'services' => $services,
            'user' => $user,
            'branch' => $user->branch,
        ]);
    }

    /**
     * Record walk-in order.
     * Creates/finds customer account, creates order, calculates pricing, records payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email'],
            'service_id' => ['required', 'exists:services,id'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'in:cash,gcash'],
            'internal_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $quantity = (float) $validated['quantity'];

        // Enforce minimum quantity rule if applicable (e.g. Wash-Dry-Fold min 5kg)
        if ($service->min_quantity && $quantity < $service->min_quantity) {
            return back()
                ->withErrors(['quantity' => "Minimum {$service->min_quantity} {$service->unit} required for {$service->name}."])
                ->withInput();
        }

        if ($service->max_quantity && $quantity > $service->max_quantity) {
            return back()
                ->withErrors(['quantity' => "Maximum {$service->max_quantity} {$service->unit} allowed for {$service->name}."])
                ->withInput();
        }

        // Find or create customer
        $email = ($validated['customer_email'] ?? null) ?: 'walkin_' . time() . '_' . rand(100, 999) . '@kapitbahay.local';
        $customer = User::firstOrCreate(
            ['phone' => $validated['customer_phone']],
            [
                'name' => $validated['customer_name'],
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'address' => 'Walk-in / In-store',
            ]
        );

        // Calculate pricing
        $subtotal = $service->price * $quantity;
        $total = $subtotal;

        $order = Order::create([
            'customer_id' => $customer->id,
            'branch_id' => $user->branch_id ?? 1,
            'status' => 'received',
            'total' => $total,
            'pickup_address' => 'Walk-in customer',
            'pickup_date' => now()->toDateString(),
            'internal_notes' => $validated['internal_notes'] ?? 'Walk-in order processed by ' . $user->name,
        ]);

        $order->services()->attach($service->id, [
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'amount' => $total,
            'status' => 'paid',
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect()->route('staff.orders.index')
            ->with('success', "Order #{$order->id} created successfully!");
    }

    /**
     * Update order status: received -> washing -> ready -> released.
     * Enforces OrderPolicy@updateStatus.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('updateStatus', $order);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:received,washing,ready,released'],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()
            ->with('success', "Order #{$order->id} updated to {$validated['status']}.");
    }
}
