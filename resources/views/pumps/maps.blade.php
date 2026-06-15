@extends('layouts.app')

@section('title', 'Pump Maps')

@section('main_class', 'w-full')

@section('content')
    <div class="w-full h-[calc(100vh-140px)] bg-gray-300 relative flex items-center justify-center">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-700 mb-2">Map Interface</h2>
            <p class="text-gray-500">Open your browser's Developer Tools (F12) and check the Console!</p>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Safely pass the PHP data directly into JavaScript
        const pumpData = @json($pumpsWithLocations);
        
        // Log it to the browser console
        console.log("Pump Data with Locations successfully loaded:", pumpData);
    </script>
@endsection