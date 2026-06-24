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
        /* Add padding inside the white card so nothing touches the edges */
        .dataTables_wrapper {
            padding: 1.5rem; 
        }
        
        /* Add space between the search bar and the table headers */
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem; 
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            padding: 4px 8px;
            border-radius: 4px;
            margin-left: 8px; /* Adds a tiny gap between "Search:" text and the input box */
        }
        
        /* Prevents Alpine flickering before load */
        [x-cloak] { display: none !important; }
    </style>

    @yield('styles')
    
</head>
<body class="bg-gray-100 min-h-screen flex flex-col font-sans" x-data="{ sidebarOpen: false, showTelegramBanner: true }">

    <nav class="bg-red-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <div class="flex items-center">
                    <a href="{{ route('pumps.maps') }}" class="flex-shrink-0">
                        <img src="https://rel.co.id/wp-content/uploads/2023/05/cropped-REL_WHITE_LOGO_ONLY.png" alt="REL Logo" class="h-10 w-auto">
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    <button onclick="openCredentialModal()" class="hidden sm:flex items-center gap-2 text-red-100 hover:text-white transition focus:outline-none" title="Update Credentials">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="text-sm font-semibold">{{ Auth::user()->first_name ?? 'Guest' }}</span>
                    </button>

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
        @yield('content')
    </main>

    <footer class="bg-red-900 text-red-200 py-6 mt-auto">
        <div class="container mx-auto px-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} REL Pump Management. All rights reserved.</p>
        </div>
    </footer>

    <div class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-4 items-end pointer-events-none w-80">
        @if(session('success'))
            <div x-data="{ showToast: true }" 
                 x-show="showToast" 
                 x-init="setTimeout(() => showToast = false, 5000)"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 translate-x-8"
                 class="pointer-events-auto relative w-full p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-xl flex items-start gap-3">
                
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <div class="flex-1 pr-4">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                <button @click="showToast = false" class="absolute top-2 right-2 text-green-600 hover:text-green-800 transition p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ showToast: true }" 
                 x-show="showToast" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="pointer-events-auto relative w-full p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-xl flex items-start gap-3">
                
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <div class="flex-1 pr-4">
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
                <button @click="showToast = false" class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ showToast: true }" 
                 x-show="showToast" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="pointer-events-auto relative w-full p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-xl flex items-start gap-3">
                
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                <div class="flex-1 pr-4">
                    <ul class="list-disc list-inside text-sm font-medium space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="showToast = false" class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if(session('telegram_prompt'))
            <div x-data="{ showTelegramBanner: true }" 
                 x-show="showTelegramBanner" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="pointer-events-auto relative w-full bg-white border border-gray-200 shadow-xl rounded-lg p-4 flex flex-col gap-3">
                
                <button @click="showTelegramBanner = false" type="button" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 transition p-1" title="Dismiss">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex items-start gap-3 mt-2">
                    <div class="bg-blue-100 p-2 rounded-full text-blue-600 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.125A59.769 59.769 0 0121.485 12 59.768 59.768 0 013.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Telegram Not Linked</h4>
                        <p class="text-xs text-gray-600 mt-1">Activate your Telegram to receive alerts.</p>
                    </div>
                </div>
                <button type="button" onclick="openGlobalTelegramModal({{ Auth::id() }})" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 rounded transition">
                    Activate Telegram
                </button>
            </div>
        @endif

    </div>



    <div id="globalTelegramModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black bg-opacity-50 transition-opacity">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Link Telegram Account
                    </h3>
                    <button type="button" onclick="closeGlobalTelegramModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-4 md:p-5 text-center">
                    <p class="text-sm text-gray-600 mb-4">Scan this QR Code using your phone's camera, or click the link to open Telegram directly.</p>
                    
                    <div id="globalQrContainer" class="flex justify-center mb-4 min-h-[200px] items-center">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <a id="globalTelegramLink" href="#" target="_blank" class="hidden text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 5.523 4.477 10 10 10s10-4.477 10-10Zm-6.521-3.693c-.66.27-3.953 1.688-9.88 4.254-.51.2-.757.397-.74.587.026.31.353.413.82.56l.164.053 1.91.63c.433.143.992.26 1.436.148.498-.125 2.378-1.554 2.476-1.636.035-.03.076-.02.054.026-.06.126-1.488 1.4-1.656 1.573-.06.062-.123.128-.066.236.057.108 1.121.724 1.706 1.11.272.18.522.344.757.498.536.35 1.01.66 1.605.606.353-.032.715-.367.904-1.393.435-2.357 1.298-7.314 1.48-9.48.016-.204-.047-.35-.157-.425-.111-.077-.282-.074-.476.012Z" clip-rule="evenodd"/>
                        </svg>
                        Open in Telegram
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openGlobalTelegramModal(userId) {
            // Show the modal
            document.getElementById('globalTelegramModal').classList.remove('hidden');
            
            // Reset the container state to loading
            document.getElementById('globalQrContainer').innerHTML = `
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-8 w-8 text-blue-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-gray-500">Generating secure link...</span>
                </div>`;
            document.getElementById('globalTelegramLink').classList.add('hidden');

            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            // Native fetch replacing the jQuery AJAX call
            fetch(`/users/${userId}/telegram-link`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.url) {
                    // Build the QR Server URL using the returned Telegram link
                    let qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(data.url);
                    
                    // Inject the image into the container
                    document.getElementById('globalQrContainer').innerHTML = `
                        <img src="${qrImageUrl}" alt="Telegram QR Code" class="w-48 h-48 border rounded shadow-sm mx-auto">
                    `;
                    
                    // Update the link button
                    let linkBtn = document.getElementById('globalTelegramLink');
                    linkBtn.href = data.url;
                    linkBtn.classList.remove('hidden');
                } else {
                    document.getElementById('globalQrContainer').innerHTML = '<p class="text-red-500 font-semibold">Failed to generate QR code.</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('globalQrContainer').innerHTML = '<p class="text-red-500 font-semibold">Network error occurred.</p>';
            });
        }

        function closeGlobalTelegramModal() {
            document.getElementById('globalTelegramModal').classList.add('hidden');
        }
    </script>

    
    @yield('scripts')

    @auth
    <div id="credentialModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black bg-opacity-50 transition-opacity">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Update Credentials</h3>
                    <button type="button" onclick="closeCredentialModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    </button>
                </div>
                
                <form action="/users/{{ Auth::id() }}/credentials" method="POST" class="p-4 md:p-5">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="return_to" value="{{ request()->fullUrl() }}">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" value="{{ Auth::user()->username }}" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" placeholder="Leave blank to keep current">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" placeholder="Confirm new password">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCredentialModal()" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCredentialModal() {
            document.getElementById('credentialModal').classList.remove('hidden');
        }
        function closeCredentialModal() {
            document.getElementById('credentialModal').classList.add('hidden');
        }
    </script>
    @endauth

</body>
</html>
