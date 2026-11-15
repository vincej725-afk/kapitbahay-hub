@extends('layouts.app', ['title' => 'Record Walk-in Order'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Record Walk-in Order</h1>
            <p class="text-sm text-slate-500">Record a new walk-in customer laundry order at {{ $branch?->name ?? 'Branch' }}.</p>
        </div>
        <a href="{{ route('staff.orders.index') }}" class="text-sm text-slate-600 hover:text-indigo-600 font-medium">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Orders
        </a>
    </div>

    <form method="POST" action="{{ route('staff.orders.store') }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 space-y-6">
        @csrf

        <!-- Customer Information -->
        <div>
            <h3 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-2 mb-4">
                <i class="fa-solid fa-user text-indigo-600 mr-1.5"></i> Customer Details
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1">Customer Full Name *</label>
                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required
                        placeholder="e.g. Maria Santos"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="customer_phone" class="block text-sm font-medium text-slate-700 mb-1">Contact / Phone Number *</label>
                    <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required
                        placeholder="e.g. 0917-123-4567"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label for="customer_email" class="block text-sm font-medium text-slate-700 mb-1">Email (Optional)</label>
                    <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}"
                        placeholder="customer@example.com (optional)"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Service & Pricing Selection -->
        <div>
            <h3 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-2 mb-4">
                <i class="fa-solid fa-tag text-indigo-600 mr-1.5"></i> Laundry Service Selection
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="service_id" class="block text-sm font-medium text-slate-700 mb-1">Select Service *</label>
                    <select id="service_id" name="service_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="">-- Choose Service --</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}" {{ old('service_id') == $svc->id ? 'selected' : '' }}>
                                {{ $svc->name }} - ₱{{ number_format($svc->price, 2) }}/{{ $svc->unit }}
                                @if($svc->min_quantity) (Min: {{ $svc->min_quantity }} {{ $svc->unit }}) @endif
                                @if($svc->max_quantity) (Max: {{ $svc->max_quantity }} {{ $svc->unit }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">Weight / Quantity *</label>
                    <input type="number" step="0.1" id="quantity" name="quantity" value="{{ old('quantity', 5) }}" required min="1"
                        placeholder="e.g. 5.5"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-slate-400 mt-1">Rule: Wash-Dry-Fold and Wash only require at least 5 kg.</p>
                </div>
            </div>
        </div>

        <!-- Payment & Notes -->
        <div>
            <h3 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-2 mb-4">
                <i class="fa-solid fa-money-bill-wave text-indigo-600 mr-1.5"></i> Payment & Notes
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-slate-700 mb-1">Payment Method *</label>
                    <select id="payment_method" name="payment_method" required class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="gcash" {{ old('payment_method') === 'gcash' ? 'selected' : '' }}>GCash</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="internal_notes" class="block text-sm font-medium text-slate-700 mb-1">Internal Notes (Staff Only)</label>
                    <textarea id="internal_notes" name="internal_notes" rows="2"
                        placeholder="e.g. White shirts separated, requested extra fabric softener"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('internal_notes') }}</textarea>
                    <p class="text-xs text-slate-400 mt-1">These notes remain internal and are not exposed via the mobile API (Question 4c).</p>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('staff.orders.index') }}" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium">
                Cancel
            </a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow-sm transition text-sm">
                Save & Print Claim Stub
            </button>
        </div>
    </form>
</div>
@endsection
