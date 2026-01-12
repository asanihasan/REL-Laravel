<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - REL Pump Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 h-screen w-full relative">
    
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <!-- Assuming image is moved to public/images/bg1.jpg -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/bg1.jpg') }}');"></div>
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/70"></div>
    </div>

    <!-- Login Card -->
    <div class="relative z-10 h-full flex items-center justify-center p-4">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl shadow-2xl w-full max-w-md">
            
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <img src="https://rel.co.id/wp-content/uploads/2023/05/cropped-REL_WHITE_LOGO_ONLY.png" alt="REL Logo" class="h-16 w-auto drop-shadow-lg">
            </div>
            
            <h1 class="text-2xl font-bold mb-6 text-center text-white tracking-wide">Sign In</h1>
            
            @if($errors->any())
                <div class="bg-red-500/20 border border-red-500 text-red-100 p-3 rounded mb-4 text-sm text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-300 uppercase mb-2 tracking-wider">Username</label>
                    <input type="text" name="username" class="w-full bg-white/10 border border-white/30 rounded px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="Enter your username" required>
                </div>
                <div class="mb-8">
                    <label class="block text-xs font-bold text-gray-300 uppercase mb-2 tracking-wider">Password</label>
                    <input type="password" name="password" class="w-full bg-white/10 border border-white/30 rounded px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition" placeholder="••••••••" required>
                </div>
                <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-bold py-3 rounded-lg shadow-lg hover:shadow-xl transition duration-200 transform hover:-translate-y-0.5">
                    Login to Dashboard
                </button>
            </form>
            
            <div class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} REL Pump Management
            </div>
        </div>
    </div>
</body>
</html>