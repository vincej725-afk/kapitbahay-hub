<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class OwnerController extends Controller
{
    /**
     * Ensure the user is the owner.
     */
    protected function authorizeOwner(): void
    {
        if (Auth::user()?->role !== 'owner') {
            abort(403, 'Unauthorized. Only the owner can access this section.');
        }
    }

    /**
     * Owner Dashboard & Sales Report across all branches.
     * Fixes Problem M1 (N+1 query problem):
     * Uses eager loading with() and pagination.
     */
    public function dashboard(Request $request): View
    {
        $this->authorizeOwner();

        $branches = Branch::all();

        // High-level sales statistics across all branches
        $totalSales = Order::sum('total');
        $totalOrdersCount = Order::count();
        $branchStats = Branch::withCount('orders')
            ->get()
            ->map(function ($branch) {
                $branch->total_revenue = Order::where('branch_id', $branch->id)->sum('total');
                return $branch;
            });

        // Query with eager loading to prevent N+1 queries (Fix A for Problem M1)
        $ordersQuery = Order::with(['services', 'customer', 'branch', 'payment'])
            ->latest();

        if ($request->filled('branch_id')) {
            $ordersQuery->where('branch_id', $request->input('branch_id'));
        }

        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->input('status'));
        }

        // Paginate results (Fix A for Problem M1)
        $orders = $ordersQuery->paginate(15)->withQueryString();

        return view('owner.dashboard', [
            'branches' => $branches,
            'branchStats' => $branchStats,
            'totalSales' => $totalSales,
            'totalOrdersCount' => $totalOrdersCount,
            'orders' => $orders,
            'selectedBranch' => $request->input('branch_id'),
        ]);
    }

    /**
     * Manage Services and Prices.
     * Only owner can change prices.
     */
    public function services(): View
    {
        $this->authorizeOwner();

        $services = Service::all();
        return view('owner.services', ['services' => $services]);
    }

    /**
     * Update service price.
     * Enforces: "Prices must be the same in all branches, and only I can change them."
     */
    public function updateService(Request $request, Service $service): RedirectResponse
    {
        $this->authorizeOwner();

        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'min_quantity' => ['nullable', 'integer', 'min:1'],
            'max_quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $service->update($validated);

        return redirect()->route('owner.services')
            ->with('success', "Price for '{$service->name}' updated to ₱" . number_format($service->price, 2) . " successfully!");
    }

    /**
     * Manage Staff Accounts.
     */
    public function staff(): View
    {
        $this->authorizeOwner();

        $staffMembers = User::where('role', 'staff')
            ->with('branch')
            ->latest()
            ->paginate(10);

        $branches = Branch::all();

        return view('owner.staff', [
            'staffMembers' => $staffMembers,
            'branches' => $branches,
        ]);
    }

    /**
     * Create new staff account assigned to a branch.
     */
    public function storeStaff(Request $request): RedirectResponse
    {
        $this->authorizeOwner();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'branch_id' => ['required', 'exists:branches,id'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'branch_id' => $validated['branch_id'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->route('owner.staff')
            ->with('success', "Staff account for '{$validated['name']}' created successfully.");
    }
}
