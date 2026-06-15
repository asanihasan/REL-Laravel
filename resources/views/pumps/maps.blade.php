@extends('layouts.app')

@section('title', 'Pump Maps')

@section('main_class', 'w-full')

@section('content')
    <div class="w-full h-[calc(100vh-140px)] bg-gray-300 relative flex items-center justify-center">
        
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-500 mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
            </svg>
            <h2 class="text-3xl font-bold text-gray-700 mb-2">Full Scale Map</h2>
            <p class="text-gray-500">Notice how this stretches edge-to-edge now!</p>
        </div>
        
    </div>
@endsection