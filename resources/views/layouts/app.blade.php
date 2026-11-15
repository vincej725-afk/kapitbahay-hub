<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Kapitbahay Laundry Hub' }} - Staff Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-indigo-700 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="flex items-center space-x-2 font-bold text-xl tracking-tight">
                    <i class="fa-solid fa-shirt text-cyan-300 text-2xl"></i>
                    <span>Kapitbahay <span class="text-cyan-300">Laundry Hub</span></span>
                </a>
                <span class="text-xs bg-indigo-900 text-indigo-200 px-2.5 py-0.5 rounded-full uppercase tracking-wider font-semibold">
                    Staff & Owner Web
                </span>
            </div>

            @auth
            <nav class="flex items-center space-x-4">
                @if(auth()->user()->role === 'owner')
                    <a href="{{ route('owner.dashboard') }}" class="px-3 py-1.5 rounded-md text-sm font-medium hover:bg-indigo-600 transition {{ request()->routeIs('owner.dashboard') ? 'bg-indigo-800' : '' }}">
                        <i class="fa-solid fa-chart-line mr-1"></i> Sales & Orders
                    </a>
                    <a href="{{ route('owner.services') }}" class="px-3 py-1.5 rounded-md text-sm font-medium hover:bg-indigo-600 transition {{ request()->routeIs('owner.services') ? 'bg-indigo-800' : '' }}">
                        <i class="fa-solid fa-tags mr-1"></i> Manage Prices
                    </a>
                    <a href="{{ route('owner.staff') }}" class="px-3 py-1.5 rounded-md text-sm font-medium hover:bg-indigo-600 transition {{ request()->routeIs('owner.staff') ? 'bg-indigo-800' : '' }}">
                        <i class="fa-solid fa-users mr-1"></i> Staff Accounts
                    </a>
                @elseif(auth()->user()->role === 'staff')
                    <a href="{{ route('staff.orders.index') }}" class="px-3 py-1.5 rounded-md text-sm font-medium hover:bg-indigo-600 transition {{ request()->routeIs('staff.orders.index') ? 'bg-indigo-800' : '' }}">
                        <i class="fa-solid fa-list-check mr-1"></i> Branch Orders
                    </a>
                    <a href="{{ route('staff.orders.create') }}" class="px-3 py-1.5 rounded-md text-sm font-medium bg-emerald-600 hover:bg-emerald-500 transition">
                        <i class="fa-solid fa-plus-circle mr-1"></i> New Walk-In Order
                    </a>
                @endif

                <div class="border-l border-indigo-500 h-6"></div>

                <div class="flex items-center space-x-3 text-sm">
                    <div class="text-right">
                        <div class="font-semibold">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-indigo-200">
                            {{ ucfirst(auth()->user()->role) }}
                            @if(auth()->user()->branch)
                                &bull; {{ auth()->user()->branch->name }}
                            @elseif(auth()->user()->role === 'owner')
                                &bull; All 3 Branches
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Logout" class="bg-indigo-800 hover:bg-red-600 p-2 rounded-lg text-white transition">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </nav>
            @endauth
        </div>
    </header>

    <!-- Flash notifications -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 w-full">
        @if(session('success'))
            <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded shadow-sm text-emerald-800 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-rose-50 border-l-4 border-rose-500 p-4 rounded shadow-sm text-rose-800 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-amber-50 border-l-4 border-amber-500 p-4 rounded shadow-sm text-amber-800">
                <div class="font-semibold mb-1 flex items-center space-x-1">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4">
            Kapitbahay Laundry Hub &bull; Cavite Branches: Dasmariñas, Imus, Bacoor &bull; Web Framework Laravel Development (WEBDEV3)
        </div>
    </footer>
</body>
</html>
