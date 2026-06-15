<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pump Manager')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            padding: 4px 8px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        /* Prevents Alpine flickering before load */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col font-sans" x-data="{ sidebarOpen: false }">

    <nav class="bg-red-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <div class="flex items-center">
                    <a href="{{ route('pumps.index') }}" class="flex-shrink-0">
                        <img src="https://rel.co.id/wp-content/uploads/2023/05/cropped-REL_WHITE_LOGO_ONLY.png" alt="REL Logo" class="h-10 w-auto">
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-sm font-semibold text-red-100 hidden sm:block">{{ Auth::user()->username ?? 'Guest' }}</span>

                    <button @click="sidebarOpen = true" class="p-2 rounded-md hover:bg-red-800 text-white focus:outline-none transition" title="Menu">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div x-show="sidebarOpen" 
         x-transition.opacity
         class="fixed inset-0 bg-black bg-opacity-50 z-40"
         @click="sidebarOpen = false"
         x-cloak></div>

    <div x-show="sidebarOpen"
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-64 bg-white shadow-xl z-50 flex flex-col"
         x-cloak>
        
        <div class="flex items-center justify-between px-4 py-4 border-b border-gray-200 bg-red-900 text-white">
            <span class="text-lg font-bold tracking-wider">MENU</span>
            <button @click="sidebarOpen = false" class="text-red-200 hover:text-white focus:outline-none transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
            
            <a href="{{ route('pumps.maps') }}" class="flex items-center gap-3 px-4 py-3 text-gray-800 font-medium hover:bg-red-50 hover:text-red-900 rounded-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
                </svg>
                Maps
            </a>

            <a href="{{ route('pumps.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-800 font-medium hover:bg-red-50 hover:text-red-900 rounded-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.737 5.1a3.375 3.375 0 0 1 2.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 0 1 .9 2.7m0 0a3 3 0 0 1-3 3m0 3h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Zm-3 6h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Z" />
                </svg>
                Fleet
            </a>

            <a href="{{ route('manage.user') }}" class="flex items-center gap-3 px-4 py-3 text-gray-800 font-medium hover:bg-red-50 hover:text-red-900 rounded-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                User
            </a>

            <a href="{{ route('manage.alert') }}" class="flex items-center gap-3 px-4 py-3 text-gray-800 font-medium hover:bg-red-50 hover:text-red-900 rounded-md transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                Alert
            </a>
            
            <hr class="my-4 border-gray-200">
            
            <form action="{{ route('logout') }}" method="POST" class="block w-full">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-red-600 font-bold hover:bg-red-50 rounded-md transition flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Log out
                </button>
            </form>
        </nav>
    </div>

    <main class="flex-grow @yield('main_class', 'container mx-auto px-4 py-8 max-w-7xl')">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-red-900 text-red-200 py-6 mt-auto">
        <div class="container mx-auto px-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} REL Pump Management. All rights reserved.</p>
        </div>
    </footer>

    @yield('scripts')

</body>
</html>