@extends('layouts.app', ['title' => 'Welcome'])

@section('content')
<div class="space-y-12 py-8">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-cyan-600 rounded-3xl text-white p-8 md:p-12 shadow-xl relative overflow-hidden">
        <div class="max-w-2xl relative z-10 space-y-4">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-cyan-200 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                Cavite's Trusted Laundry Partner
            </span>
            <h1 class="text-3xl md:text-5xl font-black tracking-tight leading-tight">
                Kapitbahay Laundry Hub
            </h1>
            <p class="text-indigo-100 text-base md:text-lg">
                Fast, reliable, and convenient laundry solutions across our 3 branches in <strong>Dasmariñas</strong>, <strong>Imus</strong>, and <strong>Bacoor</strong>. Book home pickup or visit any branch today!
            </p>
            <div class="pt-4 flex flex-wrap gap-3">
                <a href="{{ route('login') }}" class="bg-white text-indigo-700 hover:bg-indigo-50 font-bold px-6 py-3 rounded-xl shadow-md transition text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-lock"></i>
                    <span>Staff & Owner Login</span>
                </a>
                <a href="#services" class="bg-indigo-800/80 hover:bg-indigo-900 text-white font-medium px-6 py-3 rounded-xl border border-white/20 transition text-sm flex items-center space-x-2">
                    <i class="fa-solid fa-tags"></i>
                    <span>View Rates & Services</span>
                </a>
            </div>
        </div>
        <div class="hidden lg:block absolute -right-12 -bottom-16 text-indigo-500/20 text-[260px] pointer-events-none">
            <i class="fa-solid fa-soap"></i>
        </div>
    </div>

    <!-- 3 Cavite Branches -->
    <div>
        <div class="text-center max-w-xl mx-auto mb-8">
            <h2 class="text-2xl font-bold text-slate-900">Our Cavite Branches</h2>
            <p class="text-sm text-slate-500 mt-1">Conveniently located to serve 150+ families and students every day.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-900">Dasmariñas Branch</h3>
                <p class="text-xs text-slate-500 mt-1">Aguinaldo Highway, Dasmariñas, Cavite</p>
                <div class="mt-4 text-xs font-mono text-indigo-600">Tel: 0917-111-0001</div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-900">Imus Branch</h3>
                <p class="text-xs text-slate-500 mt-1">Nueno Avenue, Imus, Cavite</p>
                <div class="mt-4 text-xs font-mono text-sky-600">Tel: 0917-111-0002</div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
                <div class="w-12 h-12 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <h3 class="font-bold text-lg text-slate-900">Bacoor Branch</h3>
                <p class="text-xs text-slate-500 mt-1">Tirona Highway, Bacoor, Cavite</p>
                <div class="mt-4 text-xs font-mono text-cyan-600">Tel: 0917-111-0003</div>
            </div>
        </div>
    </div>

    <!-- Official Services & Pricing -->
    <div id="services">
        <div class="text-center max-w-xl mx-auto mb-8">
            <h2 class="text-2xl font-bold text-slate-900">Services & Transparent Pricing</h2>
            <p class="text-sm text-slate-500 mt-1">Uniform prices across all branches managed centrally by Mrs. Santos.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <span class="text-xs bg-indigo-50 text-indigo-700 font-semibold px-2.5 py-1 rounded-full uppercase">Popular</span>
                    <h4 class="font-bold text-lg text-slate-900 mt-3">Wash-Dry-Fold</h4>
                    <p class="text-xs text-slate-500 mt-1">Full laundry care washed, dried, and neatly folded.</p>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="text-2xl font-black text-indigo-600">₱35 <span class="text-xs font-normal text-slate-500">/ kg</span></div>
                    <div class="text-xs text-slate-400 mt-1">Minimum 5 kg</div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <span class="text-xs bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded-full uppercase">Basic</span>
                    <h4 class="font-bold text-lg text-slate-900 mt-3">Wash Only</h4>
                    <p class="text-xs text-slate-500 mt-1">Thorough wash cycle for your everyday clothes.</p>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="text-2xl font-black text-indigo-600">₱25 <span class="text-xs font-normal text-slate-500">/ kg</span></div>
                    <div class="text-xs text-slate-400 mt-1">Minimum 5 kg</div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <span class="text-xs bg-purple-50 text-purple-700 font-semibold px-2.5 py-1 rounded-full uppercase">Bedding</span>
                    <h4 class="font-bold text-lg text-slate-900 mt-3">Comforter</h4>
                    <p class="text-xs text-slate-500 mt-1">Heavy blankets and bulky comforters deep cleaned.</p>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="text-2xl font-black text-indigo-600">₱180 <span class="text-xs font-normal text-slate-500">/ piece</span></div>
                    <div class="text-xs text-slate-400 mt-1">Up to 10 pcs / order</div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <span class="text-xs bg-emerald-50 text-emerald-700 font-semibold px-2.5 py-1 rounded-full uppercase">Delivery</span>
                    <h4 class="font-bold text-lg text-slate-900 mt-3">Pickup & Delivery</h4>
                    <p class="text-xs text-slate-500 mt-1">Door-to-door doorstep pickup and delivery in Cavite.</p>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <div class="text-2xl font-black text-indigo-600">₱50 <span class="text-xs font-normal text-slate-500">/ trip</span></div>
                    <div class="text-xs text-slate-400 mt-1">Book today or later date</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
