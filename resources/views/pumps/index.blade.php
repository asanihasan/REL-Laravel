@extends('layouts.app')

@section('title', 'Pump List')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pump Overview</h1>
    </div>
    
    <div class="overflow-x-auto">
        <table id="pumpTable" class="display w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase border-b">
                <tr>
                    <th class="p-3 w-20">ID</th>
                    <th class="p-3 w-40">Name</th>
                    <th class="p-3 w-40">Location</th>
                    <th class="p-3 min-w-[250px]">Pump Data</th>
                    
                    <th class="p-3 min-w-[240px] whitespace-nowrap">Status</th>
                    @if(auth()->user()->hasPermission('view') || auth()->user()->hasPermission('data_manager'))
                    <th class="p-3 w-24 text-center">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($pumps as $pump)
                {{-- Dim the row slightly if the pump is inactive --}}
                <tr class="hover:bg-gray-50 transition {{ !$pump->active ? 'opacity-50 bg-gray-50' : '' }}">
                    <td class="p-3 align-top">
                        <div class="font-mono font-bold text-red-700">{{ $pump->id }}</div>
                        @if(!$pump->active)
                            <span class="text-[10px] text-gray-500 font-bold uppercase">Inactive</span>
                        @endif
                    </td>
                    
                    <td class="p-3 font-medium align-top">{{ $pump->name }}</td>
                    
                    <td class="p-3 text-gray-500 align-top">{{ $pump->location }}</td>
                
                    <td class="p-3 align-top">
                        @php 
                            $flowData = is_string($pump->pressure_or_flow) ? json_decode($pump->pressure_or_flow, true) : $pump->pressure_or_flow; 
                        @endphp
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-xs">
                            <div class="space-y-1">
                                <div class="flex justify-between border-b border-gray-100 sm:border-none pb-1 sm:pb-0">
                                    <span class="text-gray-500">Eng Speed:</span>
                                    <span class="font-medium text-gray-900">{{ $pump->rpm ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 sm:border-none pb-1 sm:pb-0">
                                    <span class="text-gray-500">Eng Load:</span>
                                    <span class="font-medium text-gray-900">{{ $pump->percent_load ?? '-' }}%</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 sm:border-none pb-1 sm:pb-0">
                                    <span class="text-gray-500">Coolant Temp:</span>
                                    <span class="font-medium text-gray-900">{{ $pump->engine_temp_mech ?? '-' }}</span>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <div class="flex justify-between border-b border-gray-100 sm:border-none pb-1 sm:pb-0">
                                    <span class="text-gray-500">Flow:</span>
                                    <span class="font-medium text-gray-900">{{ $flowData['flow'] ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-100 sm:border-none pb-1 sm:pb-0">
                                    <span class="text-gray-500">Fuel Rate:</span>
                                    <span class="font-medium text-gray-900">{{ $pump->fuel_rate ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="p-3 align-top">
                        @php
                            $autoStatus = is_string($pump->auto_manual_status) ? json_decode($pump->auto_manual_status, true) : $pump->auto_manual_status;
                            $isEngineRunning = isset($autoStatus['engine_running']) && $autoStatus['engine_running'];
                        @endphp
                        
                        <div class="flex flex-col space-y-1">
                            <div class="flex items-center justify-between text-xs min-w-[140px]">
                                <span class="text-gray-500 font-medium mr-2">Mod:</span>
                                @if(strtolower($pump->status ?? '') == 'online')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1 h-1 mr-1 bg-green-500 rounded-full flex-shrink-0"></span>
                                        <span class="last-update-time" data-time="{{ optional($pump->last_update)->toIso8601String() }}">Online</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                        <span class="w-1 h-1 mr-1 bg-red-500 rounded-full flex-shrink-0"></span>
                                        <span class="last-update-time" data-time="{{ optional($pump->last_update)->toIso8601String() }}">Offline</span>
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs min-w-[140px]">
                                <span class="text-gray-500 font-medium mr-2">Eng:</span>
                                @if($isEngineRunning)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1 h-1 mr-1 bg-green-500 rounded-full flex-shrink-0"></span> Running
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                        <span class="w-1 h-1 mr-1 bg-gray-500 rounded-full flex-shrink-0"></span> Stopped
                                    </span>
                                @endif
                            </div>
                            @if(auth()->user()->hasPermission('administrator'))
                            <div class="flex items-center justify-between text-xs min-w-[140px]">
                                <span class="text-gray-500 font-medium mr-2">Net:</span>
                                @if(strtolower($pump->connection ?? '') == 'online')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1 h-1 mr-1 bg-green-500 rounded-full flex-shrink-0"></span>
                                        <span class="last-update-time" data-time="{{ optional($pump->updated_at)->toIso8601String() }}">Online</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                        <span class="w-1 h-1 mr-1 bg-red-500 rounded-full flex-shrink-0"></span>
                                        <span class="last-update-time" data-time="{{ optional($pump->updated_at)->toIso8601String() }}">Offline</span>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between text-xs min-w-[140px] pt-1 border-t border-gray-100 mt-1">
                                <span class="text-gray-500 font-medium mr-2">SN:</span>
                                <span class="text-gray-900 font-mono text-[10px]">{{ $pump->serial_number ?? 'N/A' }}</span>
                            </div>
                            @endif
                            
                        </div>
                    </td>

                    @if(auth()->user()->hasPermission('view') || auth()->user()->hasPermission('data_manager'))
                    <td class="p-3 align-top">
                        <div class="flex items-center justify-center space-x-2">
                            @if(auth()->user()->hasPermission('view'))
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open" type="button" class="p-2 flex items-center bg-blue-50 text-blue-600 hover:bg-blue-100 rounded transition" title="View Options">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <div x-show="open" @click.outside="open = false" x-transition x-cloak class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg border border-gray-100 z-50 origin-top-right">
                                    <div class="py-1">
                                        <a href="{{ route('pumps.show', $pump->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition font-medium">
                                            Grid View
                                        </a>
                                        <a href="{{ route('pumps.show', $pump->id) }}/monitor" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition font-medium">
                                            Graph View
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(auth()->user()->hasPermission('data_manager'))
                            <button onclick="openModal('{{ $pump->id }}', '{{ addslashes($pump->name) }}', '{{ addslashes($pump->location) }}', {{ $pump->active ? 'true' : 'false' }})" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded transition" title="Update">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                    <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                                </svg>
                            </button>
                            
                            <form action="{{ route('pumps.destroy', $pump->id) }}" method="POST" onsubmit="return confirmDelete(event, '{{ addslashes($pump->name) }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded transition" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if(auth()->user()->hasPermission('data_manager'))
<div id="updateModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-96 shadow-xl transform transition-all">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Update Pump</h2>
        <form id="updateForm" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" id="modalName" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" name="location" id="modalLocation" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500">
            </div>
            
            {{-- Added Active Toggle Switch --}}
            <div class="mb-6 flex items-center justify-between">
                <label class="block text-sm font-medium text-gray-700">Pump Status (Active)</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="active" id="modalActive" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                </label>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.last-update-time').each(function() {
            const isoTime = $(this).data('time');
            
            if (isoTime) {
                const date = new Date(isoTime);
                const formattedDate = date.toLocaleString('en-GB', {
                    day: '2-digit',
                    month: '2-digit',
                    year: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }).replace(',', ''); 
                
                $(this).text(formattedDate);
            }
        });
        
        $('#pumpTable').DataTable({
            "order": [],
            "pageLength": 10,
            "autoWidth": false, 
        });
    });

    // Updated to accept isActive
    function openModal(id, name, location, isActive) {
        $('#updateForm').attr('action', '/pumps/' + id);
        $('#modalName').val(name);
        $('#modalLocation').val(location);
        
        // Check or uncheck the toggle based on the boolean
        $('#modalActive').prop('checked', isActive);
        
        $('#updateModal').removeClass('hidden');
    }

    function closeModal() {
        $('#updateModal').addClass('hidden');
    }

    // New delete confirmation function
    function confirmDelete(event, pumpName) {
        event.preventDefault(); // Stop the form from submitting immediately
        
        const userInput = prompt(`Are you sure you want to delete this pump?\nType "delete" to confirm removal of: ${pumpName}`);
        
        if (userInput !== null && userInput.trim().toLowerCase() === 'delete') {
            event.target.submit(); // Submit the specific form that triggered the event
        }
    }

    window.onclick = function(event) {
        var modal = document.getElementById('updateModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection