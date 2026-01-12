<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pump Manager')</title>
    
    <!-- CSS Dependencies -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    
    <!-- JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            padding: 4px 8px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col font-sans">

    <!-- Top Navigation Bar -->
    <nav class="bg-red-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('pumps.index') }}" class="flex-shrink-0">
                        <img src="https://rel.co.id/wp-content/uploads/2023/05/cropped-REL_WHITE_LOGO_ONLY.png" alt="REL Logo" class="h-10 w-auto">
                    </a>
                </div>

                <!-- Right Side Buttons -->
                <div class="flex items-center space-x-4">
                    <!-- Home Icon -->
                    <a href="{{ route('pumps.index') }}" class="p-2 rounded-md hover:bg-red-800 transition text-white" title="Home">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            <path d="M11.47 3.84a.75.75 0 011.06 0l8.635 8.635a.75.75 0 11-1.06 1.06l-.31-.31V20.25a.75.75 0 01-.75.75H13.5V16a1 1 0 00-1-1h-1a1 1 0 00-1 1v5h-5.5a.75.75 0 01-.75-.75V13.225l-.31.31a.75.75 0 11-1.06-1.06l8.635-8.635z" />
                        </svg>
                    </a>
                    
                    <span class="text-red-400">|</span>

                    <div class="flex items-center gap-4">
                        <!-- Username -->
                        <span class="text-sm font-semibold text-red-100 hidden sm:block">{{ Auth::user()->username ?? 'Guest' }}</span>
                        
                        <!-- Logout Button with Icon -->
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 bg-red-800 hover:bg-red-700 text-white px-3 py-1.5 rounded text-sm font-medium transition" title="Logout">
                                <span>Logout</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M7.5 3.75A1.5 1.5 0 006 5.25v13.5a1.5 1.5 0 001.5 1.5h6a1.5 1.5 0 001.5-1.5V15a.75.75 0 011.5 0v3.75a3 3 0 01-3 3h-6a3 3 0 01-3-3V5.25a3 3 0 013-3h6a3 3 0 013 3V9A.75.75 0 0115 9V5.25a1.5 1.5 0 00-1.5-1.5h-6zm10.72 4.72a.75.75 0 011.06 0l3 3a.75.75 0 010 1.06l-3 3a.75.75 0 11-1.06-1.06l1.72-1.72H9a.75.75 0 010-1.5h10.94l-1.72-1.72a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow container mx-auto px-4 py-8 max-w-7xl">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-red-900 text-red-200 py-6 mt-auto">
        <div class="container mx-auto px-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} REL Pump Management. All rights reserved.</p>
        </div>
    </footer>

    <!-- Page Specific Scripts -->
    @yield('scripts')

</body>
</html>