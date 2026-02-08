@extends('layouts.app')

@section('title', 'Pump List')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pump Overview</h1>
    </div>
    
    <div class="overflow-x-auto">
        <table id="pumpTable" class="display w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600 uppercase">
                <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">Name</th>
                    <th class="p-3">Location</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($pumps as $pump)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3 font-mono font-bold text-red-700">{{ $pump->id }}</td>
                    <td class="p-3 font-medium">{{ $pump->name }}</td>
                    <td class="p-3 text-gray-500">{{ $pump->location }}</td>
                    <td class="p-3">
                        @if($pump->status == 'online')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span> Online
                            </span>
                            <div class="text-[10px] text-gray-400 mt-1 pl-1">
                                {{ $pump->last_update->toIso8601String() }}
                            </div>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-gray-500 rounded-full"></span> Offline
                            </span>
                            <div class="text-[10px] text-gray-400 mt-1 pl-1">
                                <!-- {{ $pump->last_update->diffForHumans(null, true, true) }} ago -->
                                {{ $pump->last_update->toIso8601String() }}
                            </div>
                        @endif
                    </td>
                    <td class="p-3">
                        <div class="flex items-center space-x-2">
                            <!-- Detail Button -->
                            <a href="{{ route('pumps.show', $pump->id) }}" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded transition" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                                    <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            
                            <!-- Update Button -->
                            <button onclick="openModal('{{ $pump->id }}', '{{ $pump->name }}', '{{ $pump->location }}')" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded transition" title="Update">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                    <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                                </svg>
                            </button>
                            
                            <!-- Delete Button -->
                            <form action="{{ route('pumps.destroy', $pump->id) }}" method="POST" onsubmit="return confirm('Delete this pump?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded transition" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Update Modal (Same as before) -->
<div id="updateModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-96 shadow-xl transform transition-all">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Update Pump</h2>
        <form id="updateForm" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" id="modalName" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" name="location" id="modalLocation" class="w-full border border-gray-300 p-2 rounded focus:ring-red-500 focus:border-red-500" required>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#pumpTable').DataTable({
            "order": [],
            "pageLength": 10
        });
    });

    function openModal(id, name, location) {
        $('#updateForm').attr('action', '/pumps/' + id);
        $('#modalName').val(name);
        $('#modalLocation').val(location);
        $('#updateModal').removeClass('hidden');
    }

    function closeModal() {
        $('#updateModal').addClass('hidden');
    }

    window.onclick = function(event) {
        var modal = document.getElementById('updateModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection
