@extends('layouts.app', ['title' => 'Manage Services & Prices'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 font-semibold text-xs rounded-full uppercase tracking-wider">
                    Owner Exclusive
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Manage Service Prices</h1>
            <p class="text-sm text-slate-500">
                Rule: "Prices must be the same in all branches, and only I can change them." (Mrs. Liza Santos)
            </p>
        </div>
        <a href="{{ route('owner.dashboard') }}" class="text-sm text-slate-600 hover:text-indigo-600 font-medium">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <!-- Pricing Policy Banner -->
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-xs text-indigo-900 flex items-start space-x-3">
        <i class="fa-solid fa-circle-info text-indigo-600 text-base mt-0.5"></i>
        <div>
            <div class="font-bold text-sm mb-1">Standardized Branch Pricing Policy</div>
            <div>Any price modification made here automatically applies to all branches (Dasmariñas, Imus, Bacoor) and the customer mobile app API. Staff members do not have permission to alter service rates.</div>
        </div>
    </div>

    <!-- Services List & Edit Forms -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($services as $service)
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-slate-900">{{ $service->name }}</h3>
                        <p class="text-xs text-slate-400">Unit: Per {{ $service->unit }}</p>
                    </div>
                    <span class="text-xl font-extrabold text-indigo-600 font-mono">
                        ₱{{ number_format($service->price, 2) }}
                    </span>
                </div>

                <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg space-y-1">
                    <div class="font-medium text-slate-700">Rules & Limits:</div>
                    @if($service->min_quantity)
                        <div>&bull; Minimum: <strong>{{ $service->min_quantity }} {{ $service->unit }}</strong></div>
                    @endif
                    @if($service->max_quantity)
                        <div>&bull; Maximum: <strong>{{ $service->max_quantity }} {{ $service->unit }}</strong></div>
                    @endif
                    @if(!$service->min_quantity && !$service->max_quantity)
                        <div>&bull; Standard flat rate per {{ $service->unit }}</div>
                    @endif
                </div>

                <!-- Price Update Form -->
                <form method="POST" action="{{ route('owner.services.update', $service) }}" class="pt-2 border-t border-slate-100 flex items-center space-x-2">
                    @csrf
                    @method('PATCH')
                    <div class="flex-1">
                        <label for="price_{{ $service->id }}" class="sr-only">Price</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-400 text-xs">₱</span>
                            <input type="number" step="0.5" id="price_{{ $service->id }}" name="price" value="{{ old('price', $service->price) }}" required
                                class="w-full pl-6 pr-2 py-1.5 border border-slate-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 font-mono">
                        </div>
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-3 py-1.5 rounded text-xs transition">
                        Update Price
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
