@extends('layouts.app', ['title' => 'Sign In - Staff & Owner Portal'])

@section('content')
<div class="max-w-md mx-auto my-12 bg-white rounded-xl shadow-lg border border-slate-100 overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 p-6 text-center text-white">
        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fa-solid fa-lock text-cyan-300 text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold">Staff & Owner Portal</h2>
        <p class="text-indigo-200 text-sm mt-1">Sign in to manage laundry operations</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}" class="p-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="e.g. staff.imus@kapitbahay.ph"
                    class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input type="password" id="password" name="password" required
                    placeholder="Enter password"
                    class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                Remember me
            </label>
        </div>

        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Sign In to Staff Website
        </button>

        <div class="mt-6 border-t border-slate-100 pt-4 text-xs text-slate-500 space-y-1">
            <div class="font-semibold text-slate-700">Demo Credentials (Seeded):</div>
            <div>&bull; <strong>Owner:</strong> <code class="bg-slate-100 px-1 py-0.5 rounded text-indigo-600">liza.santos@kapitbahay.ph</code> / <code class="bg-slate-100 px-1 py-0.5 rounded">password123</code></div>
            <div>&bull; <strong>Imus Staff:</strong> <code class="bg-slate-100 px-1 py-0.5 rounded text-indigo-600">staff.imus@kapitbahay.ph</code> / <code class="bg-slate-100 px-1 py-0.5 rounded">password123</code></div>
            <div>&bull; <strong>Bacoor Staff:</strong> <code class="bg-slate-100 px-1 py-0.5 rounded text-indigo-600">staff.bacoor@kapitbahay.ph</code> / <code class="bg-slate-100 px-1 py-0.5 rounded">password123</code></div>
            <div>&bull; <strong>Dasma Staff:</strong> <code class="bg-slate-100 px-1 py-0.5 rounded text-indigo-600">staff.dasma@kapitbahay.ph</code> / <code class="bg-slate-100 px-1 py-0.5 rounded">password123</code></div>
            <div class="pt-2 text-slate-400"><em>Note: Customer accounts use the Mobile App API via Laravel Sanctum (Question 2c).</em></div>
        </div>
    </form>
</div>
@endsection
