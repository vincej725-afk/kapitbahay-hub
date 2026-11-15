@extends('layouts.app', ['title' => 'Manage Staff Accounts'])

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 font-semibold text-xs rounded-full uppercase tracking-wider">
                    Owner Exclusive
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Manage Branch Staff Accounts</h1>
            <p class="text-sm text-slate-500">
                Create and manage branch personnel accounts for Dasmariñas, Imus, and Bacoor.
            </p>
        </div>
        <a href="{{ route('owner.dashboard') }}" class="text-sm text-slate-600 hover:text-indigo-600 font-medium">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Staff Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 space-y-4">
            <h2 class="font-bold text-base text-slate-900 border-b border-slate-100 pb-2">
                <i class="fa-solid fa-user-plus text-indigo-600 mr-1.5"></i> Register Branch Staff
            </h2>

            <form method="POST" action="{{ route('owner.staff.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        placeholder="e.g. John Doe"
                        class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        placeholder="e.g. staff.new@kapitbahay.ph"
                        class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password *</label>
                    <input type="password" id="password" name="password" required minlength="6"
                        placeholder="At least 6 characters"
                        class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="branch_id" class="block text-xs font-semibold text-slate-700 mb-1">Assign Branch *</label>
                    <select id="branch_id" name="branch_id" required class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white">
                        <option value="">-- Select Branch --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Staff will only be allowed to view and update orders for this assigned branch.</p>
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                        placeholder="0917-000-0000"
                        class="w-full px-3 py-1.5 border border-slate-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded shadow-sm transition text-sm">
                    Create Staff Member
                </button>
            </form>
        </div>

        <!-- Staff List -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-4 border-b border-slate-100 font-bold text-slate-900 text-sm flex items-center justify-between">
                <span>Active Branch Staff Accounts</span>
                <span class="text-xs font-normal text-slate-400">{{ $staffMembers->total() }} accounts registered</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                            <th class="py-3 px-4 font-semibold">Staff Name</th>
                            <th class="py-3 px-4 font-semibold">Assigned Branch</th>
                            <th class="py-3 px-4 font-semibold">Email</th>
                            <th class="py-3 px-4 font-semibold">Phone</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($staffMembers as $staff)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-medium text-slate-900">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($staff->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $staff->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-block bg-sky-100 text-sky-800 text-xs px-2 py-0.5 rounded font-medium">
                                        {{ $staff->branch?->name ?? 'Unassigned' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs font-mono text-slate-600">{{ $staff->email }}</td>
                                <td class="py-3 px-4 text-xs text-slate-500">{{ $staff->phone ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">No staff accounts registered.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($staffMembers->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    {{ $staffMembers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
