@extends('layouts.app', ['title' => 'Branch Orders'])

@section('content')
<div class="space-y-6">
    <!-- Header with branch info and actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-slate-100">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 font-semibold text-xs rounded-full uppercase tracking-wider">
                    {{ $currentBranch?->name ?? 'All Branches' }}
                </span>
                <span class="text-xs text-slate-400">&bull; Staff Workspace</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Branch Laundry Orders</h1>
            <p class="text-sm text-slate-500">Record walk-in orders and update order status along the workflow.</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('staff.orders.create') }}" class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-lg shadow-sm transition text-sm">
                <i class="fa-solid fa-plus"></i>
                <span>Record Walk-in Order</span>
            </a>
        </div>
    </div>

    <!-- Order Workflow Legend -->
    <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 flex flex-wrap items-center justify-between gap-2 text-xs">
        <span class="font-semibold text-indigo-900 flex items-center">
            <i class="fa-solid fa-arrows-turn-right mr-1.5 text-indigo-600"></i> Standard Order Lifecycle:
        </span>
        <div class="flex items-center space-x-2 sm:space-x-4">
            <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-medium">1. received</span>
            <i class="fa-solid fa-arrow-right text-slate-400 text-xs"></i>
            <span class="bg-sky-100 text-sky-800 px-2 py-0.5 rounded font-medium">2. washing</span>
            <i class="fa-solid fa-arrow-right text-slate-400 text-xs"></i>
            <span class="bg-purple-100 text-purple-800 px-2 py-0.5 rounded font-medium">3. ready</span>
            <i class="fa-solid fa-arrow-right text-slate-400 text-xs"></i>
            <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-medium">4. released</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="flex items-center justify-between gap-4">
        <form method="GET" action="{{ route('staff.orders.index') }}" class="flex items-center space-x-2">
            <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-300 rounded-lg px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Statuses</option>
                <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                <option value="washing" {{ request('status') === 'washing' ? 'selected' : '' }}>Washing</option>
                <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready for Pickup</option>
                <option value="released" {{ request('status') === 'released' ? 'selected' : '' }}>Released</option>
            </select>
            @if(request('status'))
                <a href="{{ route('staff.orders.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Clear</a>
            @endif
        </form>

        <div class="text-xs text-slate-500">
            Showing {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                        <th class="py-3.5 px-4 font-semibold">Order #</th>
                        <th class="py-3.5 px-4 font-semibold">Customer</th>
                        <th class="py-3.5 px-4 font-semibold">Services & Items</th>
                        <th class="py-3.5 px-4 font-semibold">Total Amount</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-4 font-semibold">Pickup Date / Type</th>
                        <th class="py-3.5 px-4 font-semibold text-right">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-indigo-600">
                                #{{ $order->id }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-900">{{ $order->customer?->name ?? 'Walk-in' }}</div>
                                <div class="text-xs text-slate-400">{{ $order->customer?->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @foreach($order->services as $svc)
                                    <span class="inline-block bg-slate-100 text-slate-700 text-xs px-2 py-0.5 rounded mr-1 mb-1">
                                        {{ $svc->name }} ({{ (float) $svc->pivot->quantity }} {{ $svc->unit }})
                                    </span>
                                @endforeach
                                @if($order->internal_notes)
                                    <div class="text-xs text-amber-700 italic mt-0.5">Note: {{ $order->internal_notes }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                ₱{{ number_format($order->total, 2) }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($order->status === 'received')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <i class="fa-solid fa-inbox mr-1"></i> Received
                                    </span>
                                @elseif($order->status === 'washing')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-100 text-sky-800">
                                        <i class="fa-solid fa-soap mr-1"></i> Washing
                                    </span>
                                @elseif($order->status === 'ready')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                        <i class="fa-solid fa-bell mr-1"></i> Ready
                                    </span>
                                @elseif($order->status === 'released')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        <i class="fa-solid fa-circle-check mr-1"></i> Released
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-500">
                                <div>{{ $order->pickup_date?->format('M d, Y') ?? 'Today' }}</div>
                                <div class="text-slate-400">{{ Str::limit($order->pickup_address, 20) }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form method="POST" action="{{ route('staff.orders.status', $order) }}" class="inline-flex items-center space-x-1">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-xs border border-slate-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                        <option value="received" {{ $order->status === 'received' ? 'selected' : '' }}>Received</option>
                                        <option value="washing" {{ $order->status === 'washing' ? 'selected' : '' }}>Washing</option>
                                        <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                                        <option value="released" {{ $order->status === 'released' ? 'selected' : '' }}>Released</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm">
                                <i class="fa-solid fa-box-open text-3xl mb-2 block"></i>
                                No orders found for this branch.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
